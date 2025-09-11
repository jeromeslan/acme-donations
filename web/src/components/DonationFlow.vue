<template>
  <div class="donation-flow">
    <!-- Step 1: Amount Selection -->
    <div v-if="currentStep === 'amount'" class="step-content">
      <div class="step-header">
        <h3 class="step-title">Choose Your Donation Amount</h3>
        <p class="step-subtitle">Support "{{ campaign.title }}"</p>
      </div>

      <div class="campaign-progress-mini">
        <div class="progress-info">
          <span class="current">{{ formatCurrency(Number(campaign.donated_amount) || 0) }} raised</span>
          <span class="goal">of {{ formatCurrency(Number(campaign.goal_amount)) }} goal</span>
          <span v-if="isGoalReached" class="goal-reached">🎉 Goal Reached!</span>
        </div>
        <div class="progress-bar-mini">
          <div class="progress-fill-mini" :style="{ width: progressPercentage + '%' }" :class="{ 'goal-reached': isGoalReached }"></div>
        </div>
      </div>
      
      <!-- Goal Reached Warning -->
      <div v-if="isGoalReached" class="goal-reached-warning">
        <p class="warning-text">🎯 This campaign has reached its funding goal! Thank you to all donors.</p>
      </div>

      <div class="amount-selection">
        <!-- Preset Amounts -->
        <div class="preset-amounts">
          <button
            v-for="amount in presetAmounts"
            :key="amount"
            @click="selectAmount(amount)"
            :disabled="isGoalReached"
            :class="['amount-btn', { active: selectedAmount === amount && !isCustomAmount, disabled: isGoalReached }]"
          >
            {{ formatCurrency(amount) }}
          </button>
        </div>

        <!-- Custom Amount -->
        <div class="custom-amount">
          <label class="custom-label">Or enter a custom amount:</label>
          <div class="custom-input-group">
            <span class="currency-symbol">€</span>
            <input
              v-model="customAmountInput"
              @input="handleCustomAmountInput"
              @focus="isCustomAmount = true"
              :disabled="isGoalReached"
              type="number"
              min="1"
              max="10000"
              step="0.01"
              placeholder="0.00"
              class="custom-input"
            />
          </div>
          <div v-if="amountError" class="error-message">{{ amountError }}</div>
        </div>
      </div>

      <div class="step-footer">
        <button
          @click="proceedToPayment"
          :disabled="!isValidAmount || isGoalReached"
          class="btn btn-primary btn-full"
        >
          {{ isGoalReached ? 'Goal Reached' : `Proceed to Payment (${formatCurrency(finalAmount)})` }}
        </button>
      </div>
    </div>

    <!-- Step 2: Processing -->
    <div v-else-if="currentStep === 'processing'" class="step-content">
      <div class="processing-content">
        <div class="processing-spinner">
          <div class="spinner"></div>
        </div>
        <h3 class="processing-title">Processing Your Donation</h3>
        <p class="processing-message">{{ processingMessage }}</p>
        <div class="processing-details">
          <div class="detail-row">
            <span>Amount:</span>
            <span class="font-semibold">{{ formatCurrency(finalAmount) }}</span>
          </div>
          <div class="detail-row">
            <span>Campaign:</span>
            <span class="font-semibold">{{ campaign.title }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Step 3: Thank You -->
    <div v-else-if="currentStep === 'success'" class="step-content">
      <div class="success-content">
        <div class="success-icon">
          <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
          </svg>
        </div>
        <h3 class="success-title">🎉 Thank You for Your Donation!</h3>
        <p class="success-message">
          Your generous contribution of <strong>{{ formatCurrency(finalAmount) }}</strong> to
          <strong>"{{ campaign.title }}"</strong> has been successfully processed.
        </p>
        <p class="success-impact">
          Thanks to donors like you, this campaign has now raised
          <strong>{{ formatCurrency(Number(updatedCampaign?.donated_amount) || Number(campaign.donated_amount) || 0) }}</strong>
          towards its <strong>{{ formatCurrency(Number(campaign.goal_amount)) }}</strong> goal.
        </p>
        <p class="success-email">You'll receive a confirmation email shortly.</p>
      </div>

      <div class="step-footer">
        <button @click="$emit('close')" class="btn btn-secondary">
          Close
        </button>
        <button @click="viewUpdatedCampaign" class="btn btn-primary">
          View Updated Campaign
        </button>
      </div>
    </div>

    <!-- Step 4: Error -->
    <div v-else-if="currentStep === 'error'" class="step-content">
      <div class="error-content">
        <div class="error-icon">
          <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
        <h3 class="error-title">Payment Failed</h3>
        <p class="error-message">{{ errorMessage }}</p>
      </div>

      <div class="step-footer">
        <button @click="currentStep = 'amount'" class="btn btn-secondary">
          Try Again
        </button>
        <button @click="$emit('close')" class="btn btn-primary">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { api } from '@/api/client'

interface Campaign {
  id: number
  title: string
  goal_amount: string | number
  donated_amount?: string | number
  donations_count?: number
}

interface Props {
  campaign: Campaign
}

interface Emits {
  (e: 'close'): void
  (e: 'donation-success', updatedCampaign: Campaign): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

// State
const currentStep = ref<'amount' | 'processing' | 'success' | 'error'>('amount')
const selectedAmount = ref<number>(0)
const customAmountInput = ref<string>('')
const isCustomAmount = ref(false)
const amountError = ref<string>('')
const processingMessage = ref<string>('Initializing payment...')
const errorMessage = ref<string>('')
const updatedCampaign = ref<Campaign | null>(null)

// Constants
const presetAmounts = [5, 10, 20, 50]
const minAmount = 1
const maxAmount = 10000

// Computed
const progressPercentage = computed(() => {
  const goalAmount = Number(props.campaign.goal_amount) || 0
  if (!goalAmount || goalAmount === 0) return 0
  const raised = Number(props.campaign.donated_amount) || 0
  return Math.min(Math.round((raised / goalAmount) * 100), 100)
})

const isGoalReached = computed(() => {
  return progressPercentage.value >= 100
})

const finalAmount = computed(() => {
  if (isCustomAmount.value) {
    return Number(customAmountInput.value) || 0
  }
  return selectedAmount.value
})

const isValidAmount = computed(() => {
  const amount = finalAmount.value
  return amount >= minAmount && amount <= maxAmount && !amountError.value
})

// Methods
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const selectAmount = (amount: number) => {
  selectedAmount.value = amount
  isCustomAmount.value = false
  customAmountInput.value = ''
  amountError.value = ''
}

const handleCustomAmountInput = () => {
  const value = Number(customAmountInput.value)
  
  if (customAmountInput.value === '') {
    amountError.value = ''
    return
  }
  
  if (isNaN(value) || value < minAmount) {
    amountError.value = `Minimum amount is ${formatCurrency(minAmount)}`
  } else if (value > maxAmount) {
    amountError.value = `Maximum amount is ${formatCurrency(maxAmount)}`
  } else {
    amountError.value = ''
  }
}

const proceedToPayment = async () => {
  if (!isValidAmount.value) return
  
  // Check if goal is reached
  if (isGoalReached.value) {
    errorMessage.value = 'This campaign has already reached its goal. Thank you for your interest!'
    currentStep.value = 'error'
    return
  }

  currentStep.value = 'processing'
  
  try {
    // Simulate processing steps
    const steps = [
      'Initializing payment...',
      'Contacting payment gateway...',
      'Processing transaction...',
      'Updating campaign...'
    ]
    
    for (let i = 0; i < steps.length; i++) {
      processingMessage.value = steps[i]
      await new Promise(resolve => setTimeout(resolve, 800))
    }

    // Make actual donation API call
    const response = await api.post(`/api/campaigns/${props.campaign.id}/donations`, {
      amount: finalAmount.value
    })

    // Wait for processing to complete (simulate async job)
    processingMessage.value = 'Finalizing transaction...'
    await new Promise(resolve => setTimeout(resolve, 3000))

    // Fetch updated campaign data
    const updatedResponse = await api.get(`/api/campaigns/${props.campaign.id}`)
    updatedCampaign.value = updatedResponse.data
    
    console.log('Updated campaign data:', updatedCampaign.value)
    console.log('Setting currentStep to success')

    currentStep.value = 'success'
    console.log('currentStep is now:', currentStep.value)
    emit('donation-success', updatedCampaign.value)

  } catch (error: any) {
    console.error('Donation failed:', error)
    errorMessage.value = error.response?.data?.message || 'Payment failed. Please try again.'
    currentStep.value = 'error'
  }
}

const viewUpdatedCampaign = () => {
  emit('close')
  // The parent component will handle showing the updated campaign
}

// Watch for custom amount changes
watch(customAmountInput, () => {
  if (customAmountInput.value) {
    isCustomAmount.value = true
    selectedAmount.value = 0
  }
})
</script>

<style scoped>
.donation-flow {
  max-width: 100%;
}

.step-content {
  padding: 0;
}

.step-header {
  text-align: center;
  margin-bottom: 2rem;
}

.step-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.5rem;
}

.step-subtitle {
  color: #6b7280;
  font-size: 1rem;
}

.campaign-progress-mini {
  background-color: #f9fafb;
  border-radius: 0.5rem;
  padding: 1rem;
  margin-bottom: 2rem;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
  margin-bottom: 0.5rem;
}

.progress-info .current {
  font-weight: 600;
  color: #111827;
}

.progress-info .goal {
  color: #6b7280;
}

.progress-bar-mini {
  width: 100%;
  height: 0.375rem;
  background-color: #e5e7eb;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-fill-mini {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  border-radius: 9999px;
  transition: width 0.3s ease;
}

.progress-fill-mini.goal-reached {
  background: linear-gradient(90deg, #10b981, #059669);
}

.goal-reached {
  color: #10b981;
  font-weight: 600;
  font-size: 0.875rem;
}

.goal-reached-warning {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 0.5rem;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.warning-text {
  color: #166534;
  font-weight: 500;
  margin: 0;
  text-align: center;
}

.amount-selection {
  margin-bottom: 2rem;
}

.preset-amounts {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.amount-btn {
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  background-color: white;
  font-weight: 600;
  font-size: 1.125rem;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
}

.amount-btn:hover {
  border-color: #3b82f6;
  background-color: #eff6ff;
}

.amount-btn.active {
  border-color: #3b82f6;
  background-color: #3b82f6;
  color: white;
}

.amount-btn.disabled,
.amount-btn:disabled {
  background: #f3f4f6 !important;
  color: #9ca3af !important;
  border-color: #e5e7eb !important;
  cursor: not-allowed !important;
  opacity: 0.6;
}

.custom-input:disabled {
  background: #f3f4f6 !important;
  color: #9ca3af !important;
  cursor: not-allowed !important;
  opacity: 0.6;
}

.custom-amount {
  border-top: 1px solid #e5e7eb;
  padding-top: 1.5rem;
}

.custom-label {
  display: block;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.75rem;
}

.custom-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.currency-symbol {
  position: absolute;
  left: 1rem;
  color: #6b7280;
  font-weight: 500;
  z-index: 1;
}

.custom-input {
  width: 100%;
  padding: 1rem 1rem 1rem 2.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
  transition: border-color 0.2s ease;
}

.custom-input:focus {
  outline: none;
  border-color: #3b82f6;
}

.error-message {
  color: #dc2626;
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

.step-footer {
  margin-top: 2rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.btn-primary {
  background-color: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled {
  background-color: #9ca3af;
  cursor: not-allowed;
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
}

.btn-full {
  width: 100%;
}

/* Processing Step */
.processing-content {
  text-align: center;
  padding: 2rem 0;
}

.processing-spinner {
  margin-bottom: 1.5rem;
}

.spinner {
  width: 3rem;
  height: 3rem;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.processing-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.5rem;
}

.processing-message {
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.processing-details {
  background-color: #f9fafb;
  border-radius: 0.5rem;
  padding: 1rem;
  max-width: 20rem;
  margin: 0 auto;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.detail-row:last-child {
  margin-bottom: 0;
}

/* Success Step */
.success-content {
  text-align: center;
  padding: 1rem 0 2rem;
}

.success-icon {
  color: #10b981;
  margin-bottom: 1.5rem;
}

.success-icon .w-16 {
  width: 4rem;
  height: 4rem;
  margin: 0 auto;
}

.success-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 1rem;
}

.success-message {
  color: #374151;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.success-impact {
  color: #374151;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.success-email {
  color: #6b7280;
  font-size: 0.875rem;
}

/* Error Step */
.error-content {
  text-align: center;
  padding: 1rem 0 2rem;
}

.error-icon {
  color: #dc2626;
  margin-bottom: 1.5rem;
}

.error-icon .w-16 {
  width: 4rem;
  height: 4rem;
  margin: 0 auto;
}

.error-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 1rem;
}

.error-message {
  color: #dc2626;
  margin-bottom: 1rem;
}

.step-footer {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

.step-footer .btn-full + .btn {
  margin-top: 0.75rem;
}

/* Responsive */
@media (max-width: 640px) {
  .preset-amounts {
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
  }
  
  .amount-btn {
    padding: 0.75rem;
    font-size: 1rem;
  }
  
  .step-footer {
    flex-direction: column;
  }
}
</style>
