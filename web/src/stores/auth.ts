import { defineStore } from 'pinia'
import { api, initCsrf } from '@/api/client'

type User = { id: number; name: string; email: string; roles?: string[] }

export const useAuthStore = defineStore('auth', {
  state: () => ({ user: null as User | null, loading: false }),
  actions: {
    async fetchUser() {
      this.loading = true
      try {
        const { data } = await api.get<User>('/api/me')
        this.user = data
      } finally {
        this.loading = false
      }
    },
    async login(email: string, password: string) {
      await initCsrf()
      await api.post('/api/login', { email, password })
      await this.fetchUser()
    },
    async logout() {
      await api.post('/api/logout')
      this.user = null
    },
  },
})


