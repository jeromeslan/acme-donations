import { defineStore } from 'pinia'
import { api } from '@/api/client'

export type Donation = {
  id: number
  amount: number
  status: string
  campaign: { id: number; title: string }
}

export const useDonationsStore = defineStore('donations', {
  state: () => ({ items: [] as Donation[], total: 0, loading: false }),
  actions: {
    async fetchMy(page = 1) {
      this.loading = true
      try {
        const { data } = await api.get('/api/me/donations', { params: { page } })
        this.items = data.data ?? data
        this.total = data.meta?.total ?? this.items.length
      } finally {
        this.loading = false
      }
    },
    async getReceipt(donationId: number) {
      const { data } = await api.get(`/api/donations/${donationId}/receipt`)
      return data
    }
  }
})


