<template>
  <BaseCard>
    <div class="flex items-start gap-6">
      <!-- Campaign Image -->
      <div class="campaign-image flex-shrink-0">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
        </svg>
      </div>

      <!-- Campaign Content -->
      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ campaign.title }}</h3>
            <p class="text-sm text-gray-600 mb-2">by {{ campaign.creator?.name || 'Unknown' }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="badge badge-warning">{{ campaign.status }}</span>
            <span v-if="campaign.featured" class="badge badge-primary">Featured</span>
          </div>
        </div>

        <p class="text-gray-700 mb-4 line-clamp-2">{{ campaign.description }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div>
            <div class="text-sm text-gray-500">Goal Amount</div>
            <div class="font-semibold">{{ formatCurrency(campaign.goal_amount) }}</div>
          </div>
          <div>
            <div class="text-sm text-gray-500">Category</div>
            <div class="font-semibold">{{ campaign.category?.name || 'Uncategorized' }}</div>
          </div>
          <div>
            <div class="text-sm text-gray-500">Created</div>
            <div class="font-semibold">{{ formatDate(campaign.created_at) }}</div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
          <BaseButton
            variant="primary"
            size="sm"
            @click="$emit('approve', campaign.id)"
            :loading="loading"
          >
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Approve
          </BaseButton>

          <BaseButton
            variant="danger"
            size="sm"
            @click="showRejectModal = true"
            :loading="loading"
          >
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            Reject
          </BaseButton>

          <BaseButton
            tag="router-link"
            :to="`/campaign/${campaign.id}`"
            variant="secondary"
            size="sm"
          >
            View Details
          </BaseButton>
        </div>
      </div>
    </div>
  </BaseCard>

  <!-- Reject Modal -->
  <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeRejectModal">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4" @click.stop>
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Campaign</h3>
      <p class="text-gray-600 mb-4">Please provide a reason for rejecting this campaign:</p>
      
      <textarea
        v-model="rejectReason"
        placeholder="Enter rejection reason..."
        class="form-textarea mb-4"
        rows="3"
      ></textarea>

      <div class="flex items-center gap-3">
        <BaseButton
          variant="danger"
          @click="handleReject"
          :loading="loading"
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
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.mr-1 { margin-right: 0.25rem; }
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
</style>

