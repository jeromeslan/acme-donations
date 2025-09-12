<template>
  <BaseCard>
    <div class="p-4">
      <!-- Header -->
      <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
          <h3 class="text-base font-semibold text-gray-900 mb-1">{{ campaign.title }}</h3>
          <p class="text-xs text-gray-600">by {{ campaign.creator || 'Unknown' }} • {{ campaign.category || 'Uncategorized' }}</p>
        </div>
        <div class="flex items-center gap-1 ml-2">
          <span class="badge badge-warning text-xs">{{ campaign.status }}</span>
          <span v-if="campaign.featured" class="badge badge-primary text-xs">Featured</span>
        </div>
      </div>

      <!-- Description -->
      <p class="text-sm text-gray-700 mb-3 line-clamp-2">{{ campaign.description }}</p>

      <!-- Stats -->
      <div class="grid grid-cols-2 gap-3 mb-4 text-xs">
        <div>
          <div class="text-gray-500">Goal</div>
          <div class="font-semibold text-sm">{{ formatCurrency(campaign.goal_amount) }}</div>
        </div>
        <div>
          <div class="text-gray-500">Created</div>
          <div class="font-semibold text-sm">{{ formatDate(campaign.created_at) }}</div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-2">
        <BaseButton
          variant="primary"
          size="sm"
          @click="$emit('approve', campaign.id)"
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
  </BaseCard>

  <!-- Reject Modal -->
  <Teleport to="body">
    <div v-if="showRejectModal" class="modal-overlay" @click="closeRejectModal">
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
import { ref } from 'vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseButton from '@/components/ui/BaseButton.vue'

interface Campaign {
  id: number
  title: string
  description: string
  goal_amount: number
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
}

const props = defineProps<Props>()

const emit = defineEmits<{
  approve: [campaignId: number]
  reject: [campaignId: number, reason?: string]
}>()

const loading = ref(false)
const showRejectModal = ref(false)
const rejectReason = ref('')

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectReason.value = ''
}

const handleReject = () => {
  emit('reject', props.campaign.id, rejectReason.value)
  closeRejectModal()
}
</script>

<style scoped>
.w-3 { width: 0.75rem; }
.h-3 { height: 0.75rem; }
.mr-1 { margin-right: 0.25rem; }
.ml-2 { margin-left: 0.5rem; }
.gap-1 { gap: 0.25rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mb-4 { margin-bottom: 1rem; }
.p-4 { padding: 1rem; }
.text-xs { font-size: 0.75rem; line-height: 1rem; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.text-base { font-size: 1rem; line-height: 1.5rem; }
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
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-content {
  background-color: white;
  border-radius: 0.5rem;
  padding: 1.5rem;
  max-width: 28rem;
  width: 100%;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalSlideIn 0.3s ease-out;
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
    transform: scale(0.95) translateY(-10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>

