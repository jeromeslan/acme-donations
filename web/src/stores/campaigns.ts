import { defineStore } from 'pinia'
import { api } from '@/api/client'

export type Campaign = {
  id: number
  title: string
  description?: string
  goal_amount: number
  donated_amount: number
  status: 'draft'|'pending'|'active'|'completed'|'archived'
  featured: boolean
}

export const useCampaignsStore = defineStore('campaigns', {
  state: () => ({
    items: [] as Campaign[],
    featured: [] as Campaign[],
    page: 1,
    total: 0,
    loading: false,
  }),
  actions: {
    async fetch(params: Record<string, unknown> = {}) {
      this.loading = true
      try {
        const { data } = await api.get('/api/campaigns', { params })
        this.items = data.data ?? data
        this.total = data.meta?.total ?? this.items.length
      } finally { this.loading = false }
    },
    async fetchFeatured() {
      const { data } = await api.get('/api/campaigns/featured')
      this.featured = data
    },
    async getOne(id: number) {
      const { data } = await api.get(`/api/campaigns/${id}`)
      return data as Campaign
    },
    async donate(campaignId: number, amount: number) {
      await api.post(`/api/campaigns/${campaignId}/donations`, { amount })
    },
  }
})


