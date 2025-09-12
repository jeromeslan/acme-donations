<template>
  <div class="campaign-card">
    <div class="campaign-content">
      <div class="flex items-start justify-between mb-3">
        <h3 class="campaign-title">{{ campaign.title }}</h3>
        <span v-if="campaign.featured" class="badge badge-primary text-xs">Featured</span>
      </div>

      <p class="campaign-description">{{ campaign.description }}</p>

      <div class="campaign-progress mb-4">
        <div class="flex justify-between items-center mb-2">
          <span class="text-sm font-medium text-gray-700">Progress</span>
          <span class="text-sm text-gray-600">{{ progressPercentage }}%</span>
        </div>
        <div class="progress">
          <div class="progress-bar" :style="{ width: progressPercentage + '%' }"></div>
        </div>
      </div>

      <div class="campaign-stats">
        <div class="flex justify-between items-center">
          <div>
            <div class="text-lg font-semibold text-gray-900">{{ formatCurrency(Number(campaignData.donated_amount) || 0) }}</div>
            <div class="text-sm text-gray-600">raised of {{ formatCurrency(Number(campaignData.goal_amount)) }}</div>
          </div>
          <div class="text-right">
            <div class="text-lg font-semibold text-primary-600">{{ campaignData.donations_count || 0 }}</div>
            <div class="text-sm text-gray-600">donor{{ (campaignData.donations_count || 0) === 1 ? '' : 's' }}</div>
          </div>
        </div>
      </div>

      <div class="mt-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span :class="statusBadgeClass" class="badge text-xs">
            {{ statusLabel }}
          </span>
          <span class="text-xs text-gray-500">{{ campaign.category?.name }}</span>
        </div>
        
        <BaseButton
          @click="showModal = true"
          variant="primary"
          size="sm"
        >
          View Details
        </BaseButton>
      </div>

      <!-- Featured Campaign Option (only for pending campaigns) -->
      <div v-if="showActions" class="mt-4 p-3 bg-gray-50 rounded-lg">
        <label class="flex items-center gap-2">
          <input
            v-model="isFeatured"
            type="checkbox"
            class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
          />
          <span class="text-sm text-gray-700">Featured campaign</span>
        </label>
        <p class="text-xs text-gray-500 mt-1">Featured campaigns are highlighted on the homepage</p>
      </div>

      <!-- Action Buttons (only for pending campaigns) -->
      <div v-if="showActions" class="mt-4 flex items-center gap-2">
        <BaseButton
          variant="primary"
          size="sm"
          @click="$emit('approve', campaign.id, isFeatured)"
          :loading="loading"
          class="flex-1"
        >
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          Approve
        </BaseButton>

        <BaseButton
          variant="danger"
          size="sm"
          @click="showRejectModal = true"
          :loading="loading"
          class="flex-1 reject-button"
        >
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
          Reject
        </BaseButton>
      </div>
    </div>
  </div>

  <!-- Campaign Details Modal -->
  <Teleport to="body">
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">{{ campaignData.title }}</h2>
          <button @click="closeModal" class="modal-close-btn" aria-label="Close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="campaign-info">
            <div class="flex items-center gap-3 mb-4">
              <span :class="statusBadgeClass" class="badge">{{ statusLabel }}</span>
              <span v-if="campaignData.featured" class="badge badge-primary">Featured</span>
              <span class="text-sm text-gray-600">{{ campaignData.category?.name }}</span>
            </div>
            
            <p class="text-gray-700 mb-6 leading-relaxed">{{ campaignData.description }}</p>
            
            <div class="progress-section mb-6">
              <div class="flex justify-between items-center mb-2">
                <span class="font-medium text-gray-700">Progress</span>
                <span class="text-sm font-medium text-gray-600">{{ progressPercentage }}%</span>
              </div>
              <div class="progress-bar-container">
                <div class="progress-bar-fill" :style="{ width: progressPercentage + '%' }"></div>
              </div>
            </div>
            
            <div class="stats-grid">
              <div class="stat-item">
                <div class="stat-value">{{ formatCurrency(Number(campaignData.donated_amount) || 0) }}</div>
                <div class="stat-label">Raised</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ formatCurrency(Number(campaignData.goal_amount)) }}</div>
                <div class="stat-label">Goal</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ campaignData.donations_count || 0 }}</div>
                <div class="stat-label">Donor{{ (campaignData.donations_count || 0) === 1 ? '' : 's' }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <BaseButton @click="closeModal" variant="secondary" size="sm">
            Close
          </BaseButton>
        </div>
      </div>
    </div>
  </Teleport>

  <!-- Reject Modal (only for pending campaigns) -->
  <Teleport to="body">
    <div v-if="showActions && showRejectModal" class="modal-overlay" @click="closeRejectModal">
      <div class="modal-content" @click.stop>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Campaign</h3>
        <p class="text-gray-600 mb-4">Please provide a reason for rejecting this campaign:</p>
        
        <textarea
          v-model="rejectReason"
          placeholder="Enter rejection reason..."
          class="modal-textarea"
          rows="3"
        ></textarea>

        <div class="modal-buttons">
          <BaseButton
            variant="danger"
            @click="handleReject"
            :loading="loading"
            class="reject-button"
          >
            Reject Campaign
          </BaseButton>
          <BaseButton
            variant="secondary"
            @click="closeRejectModal"
          >
            Cancel
          </BaseButton>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, ref, withDefaults, onMounted, onUnmounted } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'

interface Campaign {
  id: number
  title: string
  description: string
  goal_amount: string | number
  donated_amount?: string | number
  donations_count?: number
  status: string
  featured: boolean
  created_at: string
  creator?: {
    name: string
  }
  category?: {
    name: string
  }
}

interface Props {
  campaign: Campaign
  showActions?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showActions: true
})

const emit = defineEmits<{
  approve: [campaignId: number, featured?: boolean]
  reject: [campaignId: number, reason?: string]
}>()

const loading = ref(false)
const showModal = ref(false)
const showRejectModal = ref(false)
const rejectReason = ref('')
const isFeatured = ref(false)
const campaignData = ref<Campaign>(props.campaign)

const closeModal = () => {
  showModal.value = false
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectReason.value = ''
}

const handleReject = () => {
  emit('reject', props.campaign.id, rejectReason.value)
  closeRejectModal()
}

const handleEscapeKey = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    if (showRejectModal.value) {
      closeRejectModal()
    } else if (showModal.value) {
      closeModal()
    }
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})

const progressPercentage = computed(() => {
  const goalAmount = Number(campaignData.value.goal_amount) || 0
  if (!goalAmount || goalAmount === 0) return 0
  const raised = Number(campaignData.value.donated_amount) || 0
  return Math.min(Math.round((raised / goalAmount) * 100), 100)
})

const statusLabel = computed(() => {
  switch (props.campaign.status) {
    case 'draft': return 'Draft'
    case 'pending': return 'Pending Review'
    case 'published': return 'Active'
    case 'active': return 'Active'
    case 'completed': return 'Completed'
    case 'rejected': return 'Rejected'
    default: return props.campaign.status
  }
})

const statusBadgeClass = computed(() => {
  switch (props.campaign.status) {
    case 'published':
    case 'active':
      return 'badge-success'
    case 'pending':
      return 'badge-warning'
    case 'draft':
      return 'badge-primary'
    case 'completed':
      return 'badge-primary'
    case 'rejected':
      return 'badge-error'
    default:
      return 'badge-primary'
  }
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}
</script>

<style scoped>
.w-3 { width: 0.75rem; }
.h-3 { height: 0.75rem; }
.w-6 { width: 1.5rem; }
.h-6 { height: 1.5rem; }
.mr-1 { margin-right: 0.25rem; }
.ml-2 { margin-left: 0.5rem; }
.gap-1 { gap: 0.25rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mb-4 { margin-bottom: 1rem; }
.mb-6 { margin-bottom: 1.5rem; }
.mt-4 { margin-top: 1rem; }
.mt-6 { margin-top: 1.5rem; }
.p-3 { padding: 0.75rem; }
.p-4 { padding: 1rem; }
.text-xs { font-size: 0.75rem; line-height: 1rem; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.text-base { font-size: 1rem; line-height: 1.5rem; }
.text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.flex-shrink-0 { flex-shrink: 0; }
.min-w-0 { min-width: 0; }
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.fixed { position: fixed; }
.inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
.z-50 { z-index: 50; }
.bg-opacity-50 { background-opacity: 0.5; }
.max-w-md { max-width: 28rem; }
.mx-4 { margin-left: 1rem; margin-right: 1rem; }
.rounded-lg { border-radius: 0.5rem; }
.bg-gray-50 { background-color: #f9fafb; }

/* Custom hover style for reject button - Override BaseButton styles */
.reject-button:hover {
  background-color: #f97316 !important; /* Orange-500 */
  border-color: #f97316 !important;
  color: white !important;
  transform: translateY(-1px);
  transition: all 0.2s ease-in-out;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
}

.reject-button:active {
  background-color: #ea580c !important; /* Orange-600 */
  border-color: #ea580c !important;
  color: white !important;
  transform: translateY(0);
}

.reject-button:focus {
  box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.5) !important;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
  overflow-y: auto;
}

.modal-content {
  background-color: white;
  border-radius: 0.75rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  max-width: 42rem;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: modalSlideIn 0.3s ease-out;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 1.5rem 0 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.modal-close-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}

.modal-close-btn:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 0 1.5rem;
}

.campaign-info {
  /* Already styled via Tailwind classes */
}

.progress-section {
  /* Already styled via Tailwind classes */
}

.progress-bar-container {
  width: 100%;
  background-color: #e5e7eb;
  border-radius: 9999px;
  height: 0.5rem;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
  border-radius: 9999px;
  transition: width 0.3s ease;
}

.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 1.5rem;
  padding: 1.5rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  margin-top: 1.5rem;
}

.modal-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  margin-bottom: 1rem;
  resize: vertical;
  font-family: inherit;
  font-size: 0.875rem;
}

.modal-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-buttons {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

/* Responsive */
@media (max-width: 640px) {
  .modal-content {
    margin: 0.5rem;
    max-height: 95vh;
  }
  
  .modal-header,
  .modal-body,
  .modal-footer {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
    padding: 1rem;
  }
  
  .modal-title {
    font-size: 1.25rem;
  }
}
</style>

