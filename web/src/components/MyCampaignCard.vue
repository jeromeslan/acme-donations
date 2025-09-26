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
            <div class="text-lg font-semibold text-gray-900">{{ formatCurrency(Number(campaign.donated_amount) || 0) }}</div>
            <div class="text-sm text-gray-600">raised of {{ formatCurrency(Number(campaign.goal_amount)) }}</div>
          </div>
          <div class="text-right">
            <div class="text-lg font-semibold text-primary-600">{{ campaign.donations_count || 0 }}</div>
            <div class="text-sm text-gray-600">donor{{ (campaign.donations_count || 0) === 1 ? '' : 's' }}</div>
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
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-2">
                <span v-if="campaignData.featured" class="badge badge-primary">Featured</span>
                <span class="text-sm text-gray-600">{{ campaignData.category?.name }}</span>
              </div>
              <span :class="getStatusBadgeClass(campaignData.status)" class="status-badge-modal">
                {{ getStatusLabel(campaignData.status) }}
              </span>
            </div>
            
            <p class="text-gray-700 mb-6 leading-relaxed">{{ campaignData.description }}</p>

            <div class="grid grid-cols-2 gap-4 mb-6">
              <div class="stat-block">
                <div class="stat-value">{{ formatCurrency(Number(campaignData.donated_amount) || 0) }}</div>
                <div class="stat-label">raised of {{ formatCurrency(Number(campaignData.goal_amount) || 0) }}</div>
              </div>
              <div class="stat-block">
                <div class="stat-value">{{ campaignData.donations_count || 0 }}</div>
                <div class="stat-label">donors</div>
              </div>
            </div>

            <div class="progress-section mb-6">
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm font-medium text-gray-700">Progress</span>
                <span class="text-sm font-medium text-gray-700">{{ progressPercentageModal }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: progressPercentageModal + '%' }"></div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <BaseButton @click="closeModal" variant="secondary" size="sm">
              Close
            </BaseButton>
            <BaseButton
              @click="startDonation"
              variant="primary"
              size="sm"
              :disabled="campaignData.status !== 'active'"
            >
              {{ campaignData.status === 'active' ? 'Donate Now' : 'Campaign Not Active' }}
            </BaseButton>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Donation Flow Modal -->
    <Teleport to="body">
      <div v-if="showDonationModal" class="modal-overlay" @click="closeDonationModal">
        <div class="donation-modal-content" @click.stop>
          <div class="donation-modal-header">
            <h2 class="donation-modal-title">Make a Donation</h2>
            <button @click="closeDonationModal" class="modal-close-btn" aria-label="Close">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div class="donation-modal-body">
            <DonationFlow 
              :campaign="campaignData" 
              @close="closeDonationModal"
              @donation-success="handleDonationSuccess"
            />
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import DonationFlow from '@/components/DonationFlow.vue'

interface Campaign {
  id: number
  title: string
  description: string
  goal_amount: string | number
  donated_amount?: string | number
  donations_count?: number
  status: string
  featured: boolean
  category?: {
    name: string
  }
  rejection_reason?: string
}

interface Props {
  campaign: Campaign
}

interface Emits {
  'campaign-updated': [campaign: Campaign]
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const showModal = ref(false)
const showDonationModal = ref(false)
const campaignData = ref<Campaign>({ ...props.campaign })

// Watch for changes in the prop and update local reactive data
watch(() => props.campaign, (newCampaign) => {
  campaignData.value = { ...newCampaign }
}, { deep: true, immediate: true })

const progressPercentage = computed(() => {
  const goal = Number(campaignData.value.goal_amount)
  const raised = Number(campaignData.value.donated_amount) || 0
  if (goal === 0) return 0
  return Math.min(100, Math.round((raised / goal) * 100))
})

const progressPercentageModal = computed(() => {
  const goal = Number(campaignData.value.goal_amount)
  const raised = Number(campaignData.value.donated_amount) || 0
  if (goal === 0) return 0
  return Math.min(100, Math.round((raised / goal) * 100))
})

const statusLabel = computed(() => {
  return campaignData.value.status === 'active' ? 'Active' : getStatusLabel(campaignData.value.status)
})

const statusBadgeClass = computed(() => {
  return campaignData.value.status === 'active' ? 'badge-success' : 'badge-secondary'
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const getStatusLabel = (status: string) => {
  switch (status) {
    case 'active': return 'Active'
    case 'pending': return 'Pending Review'
    case 'draft': return 'Draft'
    case 'archived': return 'Rejected'
    case 'completed': return 'Completed'
    default: return 'Unknown'
  }
}

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'active': return 'bg-green-100 text-green-800'
    case 'pending': return 'bg-yellow-100 text-yellow-800'
    case 'draft': return 'bg-blue-100 text-blue-800'
    case 'archived': return 'bg-red-100 text-red-800'
    case 'completed': return 'bg-purple-100 text-purple-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

const closeModal = () => {
  showModal.value = false
}

const closeDonationModal = () => {
  showDonationModal.value = false
}

const startDonation = () => {
  // Only allow donations for active campaigns
  if (campaignData.value.status !== 'active') {
    return
  }

  showModal.value = false // Close campaign details modal
  showDonationModal.value = true // Open donation modal
}

const handleDonationSuccess = (updatedCampaign: Campaign) => {
  // Update the local campaign data with the new amounts
  campaignData.value = updatedCampaign
  
  // Emit the updated campaign to the parent component
  emit('campaign-updated', updatedCampaign)
  
  // Don't close the modal automatically - let the user see the thank you message
  // The modal will be closed when the user clicks "Close" or "View Updated Campaign"
  // The campaign cards will automatically update due to reactivity
}
</script>

<style scoped>
/* Modal styles - using the same styles as CampaignCard */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 0.75rem;
  max-width: 600px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
  padding: 1.5rem 1.5rem 0 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
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
  transition: all 0.2s;
}

.modal-close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}


.status-badge-modal {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-block {
  background-color: #f9fafb;
  padding: 1rem;
  border-radius: 0.5rem;
  text-align: center;
}

.stat-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: #111827;
}

.stat-label {
  font-size: 0.75rem;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.modal-footer {
  padding: 0 1.5rem 1.5rem 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

/* Donation modal styles */
.donation-modal-content {
  background: white;
  border-radius: 0.75rem;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.donation-modal-header {
  padding: 1.5rem 1.5rem 0 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.donation-modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0;
}

.donation-modal-body {
  padding: 1.5rem;
}
</style>