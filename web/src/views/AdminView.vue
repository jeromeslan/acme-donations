<template>
  <div class="page-layout">
    <AppNavbar />
    
    <main class="page-content">
      <div class="container">
        <!-- Page Header -->
        <div class="page-header mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
          <p class="text-gray-600">Manage campaigns, donations, and platform statistics</p>
        </div>

        <!-- Stats Grid -->
        <div class="dashboard-grid mb-12">
          <div class="stat-card">
            <div class="flex items-center justify-between">
              <div>
                <div class="stat-value">{{ stats.totalCampaigns || 0 }}</div>
                <div class="stat-label">Total Campaigns</div>
              </div>
              <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="flex items-center justify-between">
              <div>
                <div class="stat-value">{{ stats.publishedCampaigns || 0 }}</div>
                <div class="stat-label">Published Campaigns</div>
              </div>
              <div class="w-12 h-12 bg-success-100 text-success-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="flex items-center justify-between">
              <div>
                <div class="stat-value">{{ stats.pendingCampaigns || 0 }}</div>
                <div class="stat-label">Pending Review</div>
              </div>
              <div class="w-12 h-12 bg-warning-100 text-warning-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <div class="stat-card">
            <div class="flex items-center justify-between">
              <div>
                <div class="stat-value">{{ formatCurrency(stats.totalRaised || 0) }}</div>
                <div class="stat-label">Total Raised</div>
              </div>
              <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Campaigns Section -->
        <section v-if="pendingCampaigns.length > 0" class="mb-12">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-900">Campaigns Pending Review</h2>
            <span class="badge badge-warning">{{ pendingCampaigns.length }} pending</span>
          </div>

          <div class="space-y-4">
            <AdminCampaignCard
              v-for="campaign in pendingCampaigns"
              :key="campaign.id"
              :campaign="campaign"
              @approve="handleApproveCampaign"
              @reject="handleRejectCampaign"
            />
          </div>
        </section>

        <!-- Recent Activity -->
        <section>
          <h2 class="text-2xl font-semibold text-gray-900 mb-6">Recent Activity</h2>
          
          <BaseCard>
            <div v-if="loading" class="text-center py-8">
              <div class="loading"></div>
              <p class="mt-4 text-gray-600">Loading recent activity...</p>
            </div>
            
            <div v-else-if="recentActivity.length === 0" class="text-center py-8">
              <div class="text-gray-400 mb-4">
                <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd" />
                </svg>
              </div>
              <p class="text-gray-600">No recent activity</p>
            </div>

            <div v-else class="space-y-4">
              <div v-for="activity in recentActivity" :key="activity.id" class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-b-0">
                <div class="w-2 h-2 bg-primary-600 rounded-full"></div>
                <div class="flex-1">
                  <p class="text-sm text-gray-900">{{ activity.description }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                </div>
              </div>
            </div>
          </BaseCard>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { api } from '@/api/client'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import BaseCard from '@/components/ui/BaseCard.vue'
import AdminCampaignCard from '@/components/AdminCampaignCard.vue'

const loading = ref(false)
const stats = ref({})
const pendingCampaigns = ref([])
const recentActivity = ref([])

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
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const [statsResponse, campaignsResponse] = await Promise.all([
      api.get('/api/admin/dashboard'),
      api.get('/api/admin/campaigns/pending')
    ])
    
    stats.value = statsResponse.data.stats || {}
    pendingCampaigns.value = campaignsResponse.data.campaigns || []
    recentActivity.value = statsResponse.data.recentActivity || []
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

const handleApproveCampaign = async (campaignId: number) => {
  try {
    await api.post(`/api/admin/campaigns/${campaignId}/approve`)
    // Refresh data
    await fetchDashboardData()
  } catch (error) {
    console.error('Error approving campaign:', error)
  }
}

const handleRejectCampaign = async (campaignId: number, reason?: string) => {
  try {
    await api.post(`/api/admin/campaigns/${campaignId}/reject`, { reason })
    // Refresh data
    await fetchDashboardData()
  } catch (error) {
    console.error('Error rejecting campaign:', error)
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.w-6 { width: 1.5rem; }
.h-6 { height: 1.5rem; }
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.w-2 { width: 0.5rem; }
.h-2 { height: 0.5rem; }
.rounded-lg { border-radius: 0.5rem; }
.space-y-4 > * + * { margin-top: 1rem; }
.last\\:border-b-0:last-child { border-bottom-width: 0; }
</style>