<template>
  <div class="campaign-card">
    <div class="campaign-image">
      <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
      </svg>
    </div>

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
  </div>

  <!-- Campaign Details Modal -->
  <Teleport to="body">
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">{{ campaign.title }}</h2>
          <button @click="closeModal" class="modal-close-btn" aria-label="Close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="modal-body">
          <div class="campaign-image-large">
            <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
          </div>
          
          <div class="campaign-info">
            <div class="flex items-center gap-3 mb-4">
              <span :class="statusBadgeClass" class="badge">{{ statusLabel }}</span>
              <span v-if="campaign.featured" class="badge badge-primary">Featured</span>
              <span class="text-sm text-gray-600">{{ campaign.category?.name }}</span>
            </div>
            
            <p class="text-gray-700 mb-6 leading-relaxed">{{ campaign.description }}</p>
            
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
                <div class="stat-value">{{ formatCurrency(Number(campaign.donated_amount) || 0) }}</div>
                <div class="stat-label">Raised</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ formatCurrency(Number(campaign.goal_amount)) }}</div>
                <div class="stat-label">Goal</div>
              </div>
              <div class="stat-item">
                <div class="stat-value">{{ campaign.donations_count || 0 }}</div>
                <div class="stat-label">Donor{{ (campaign.donations_count || 0) === 1 ? '' : 's' }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <BaseButton @click="closeModal" variant="secondary" size="sm">
            Close
          </BaseButton>
          <BaseButton variant="primary" size="sm">
            Donate Now
          </BaseButton>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'

const showModal = ref(false)

const closeModal = () => {
  showModal.value = false
}

const handleEscapeKey = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && showModal.value) {
    closeModal()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
})

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
}

interface Props {
  campaign: Campaign
}

const props = defineProps<Props>()

const progressPercentage = computed(() => {
  const goalAmount = Number(props.campaign.goal_amount) || 0
  if (!goalAmount || goalAmount === 0) return 0
  const raised = Number(props.campaign.donated_amount) || 0
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
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.w-20 { width: 5rem; }
.h-20 { height: 5rem; }
.w-6 { width: 1.5rem; }
.h-6 { height: 1.5rem; }

/* Modal Styles */
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

.campaign-image-large {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f3f4f6;
  border-radius: 0.5rem;
  padding: 2rem;
  margin-bottom: 1.5rem;
  color: #6b7280;
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

