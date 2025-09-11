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
            <div class="text-lg font-semibold text-gray-900">{{ formatCurrency(campaign.raised_amount || 0) }}</div>
            <div class="text-sm text-gray-600">raised of {{ formatCurrency(campaign.goal_amount) }}</div>
          </div>
          <div class="text-right">
            <div class="text-lg font-semibold text-primary-600">{{ campaign.donations_count || 0 }}</div>
            <div class="text-sm text-gray-600">donations</div>
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
          tag="router-link"
          :to="`/campaign/${campaign.id}`"
          variant="primary"
          size="sm"
        >
          View Details
        </BaseButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import BaseButton from '@/components/ui/BaseButton.vue'

interface Campaign {
  id: number
  title: string
  description: string
  goal_amount: number
  raised_amount?: number
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
  if (!props.campaign.goal_amount || props.campaign.goal_amount === 0) return 0
  const raised = props.campaign.raised_amount || 0
  return Math.min(Math.round((raised / props.campaign.goal_amount) * 100), 100)
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
</style>

