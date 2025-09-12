<template>
  <section v-if="campaigns.length > 0" class="py-16">
    <div class="container">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">My Campaigns</h2>
        <p class="text-lg text-gray-600">Track and manage your charitable campaigns</p>
      </div>

      <div class="campaign-grid">
        <MyCampaignCard
          v-for="campaign in campaigns"
          :key="campaign.id"
          :campaign="campaign"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import MyCampaignCard from '@/components/MyCampaignCard.vue'

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
  rejection_reason?: string
  category?: {
    name: string
  }
}

interface Props {
  campaigns: Campaign[]
}

defineProps<Props>()
</script>

<style scoped>
/* No custom styles needed - using global campaign-grid and campaign-card styles */
</style>
