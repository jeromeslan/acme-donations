<template>
  <div class="page-layout">
    <AppNavbar />
    
    <main class="page-content">
      <!-- Page Header -->
      <section class="bg-gradient-to-r from-primary-600 to-primary-700 text-white py-20">
        <div class="container text-center">
          <h1 class="text-4xl font-bold mb-6">Admin Campaign Management</h1>
          <p class="text-xl mb-8 max-w-2xl mx-auto">
            Manage and oversee all campaigns on the platform
          </p>
          
          <!-- Admin Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div class="stat-item">
              <div class="stat-value text-3xl font-bold">{{ formatCurrency(globalStats.totalRaised) }}</div>
              <div class="stat-label text-primary-100">Total Raised</div>
            </div>
            <div class="stat-item">
              <div class="stat-value text-3xl font-bold">{{ globalStats.totalCampaigns }}</div>
              <div class="stat-label text-primary-100">All Campaigns</div>
            </div>
            <div class="stat-item">
              <div class="stat-value text-3xl font-bold">{{ globalStats.totalDonations }}</div>
              <div class="stat-label text-primary-100">Total Donations</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Featured Campaigns -->
      <section v-if="featuredCampaigns.length > 0" class="py-16">
        <div class="container">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Campaigns</h2>
            <p class="text-lg text-gray-600">Campaigns highlighted on the homepage</p>
          </div>

          <div class="campaign-grid">
            <AdminCampaignCard
              v-for="campaign in featuredCampaigns"
              :key="campaign.id"
              :campaign="campaign"
              :show-actions="false"
            />
          </div>
        </div>
      </section>

      <!-- All Campaigns -->
      <section class="py-16 bg-gray-50">
        <div class="container">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">All Campaigns</h2>
            <p class="text-lg text-gray-600">View and manage all campaigns regardless of status</p>
          </div>

          <div v-if="loading" class="text-center py-12">
            <div class="loading"></div>
            <p class="mt-4 text-gray-600">Loading campaigns...</p>
          </div>

          <div v-else-if="allCampaigns.length === 0" class="text-center py-12">
            <div class="text-gray-400 mb-4">
              <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No campaigns found</h3>
            <p class="text-gray-600">No campaigns have been created yet.</p>
          </div>

          <div v-else class="campaign-grid">
            <AdminCampaignCard
              v-for="campaign in allCampaigns"
              :key="campaign.id"
              :campaign="campaign"
              :show-actions="campaign.status === 'pending'"
              @approve="handleApproveCampaign"
              @reject="handleRejectCampaign"
            />
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { api } from '@/api/client'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import AdminCampaignCard from '@/components/AdminCampaignCard.vue'

const toast = useToast()
const loading = ref(false)
const allCampaigns = ref([])
const featuredCampaigns = ref([])
const globalStats = ref({
  totalRaised: 0,
  totalCampaigns: 0,
  totalDonations: 0
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const fetchAllCampaigns = async () => {
  loading.value = true
  try {
    const response = await api.get('/api/admin/campaigns/all')
    allCampaigns.value = response.data.campaigns || []
  } catch (error) {
    console.error('Error fetching all campaigns:', error)
    allCampaigns.value = []
  } finally {
    loading.value = false
  }
}

const fetchFeaturedCampaigns = async () => {
  try {
    const response = await api.get('/api/campaigns/featured')
    featuredCampaigns.value = response.data
  } catch (error) {
    console.error('Error fetching featured campaigns:', error)
    featuredCampaigns.value = []
  }
}

const fetchGlobalStats = async () => {
  try {
    const response = await api.get('/api/stats')
    globalStats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const handleApproveCampaign = async (campaignId: number, featured: boolean = false) => {
  try {
    await api.post(`/api/admin/campaigns/${campaignId}/approve`, {
      featured: featured
    })
    toast.success('Campaign approved successfully! It is now published and visible to all users. ✅', {
      timeout: 5000
    })
    // Refresh data
    await fetchAllCampaigns()
  } catch (error) {
    console.error('Error approving campaign:', error)
    toast.error('Failed to approve campaign. Please try again.')
  }
}

const handleRejectCampaign = async (campaignId: number, reason?: string) => {
  try {
    await api.post(`/api/admin/campaigns/${campaignId}/reject`, { reason })
    toast.success('Campaign rejected successfully. The creator has been notified. 🚫', {
      timeout: 4000
    })
    // Refresh data
    await fetchAllCampaigns()
  } catch (error) {
    console.error('Error rejecting campaign:', error)
    toast.error('Failed to reject campaign. Please try again.')
  }
}

onMounted(async () => {
  await Promise.all([
    fetchAllCampaigns(),
    fetchFeaturedCampaigns(),
    fetchGlobalStats()
  ])
})
</script>

<style scoped>
.grid {
  display: grid;
}

.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

.gap-8 {
  gap: 2rem;
}

.max-w-2xl {
  max-width: 42rem;
}

.max-w-4xl {
  max-width: 56rem;
}

.mx-auto {
  margin-left: auto;
  margin-right: auto;
}

.py-20 {
  padding-top: 5rem;
  padding-bottom: 5rem;
}

.py-16 {
  padding-top: 4rem;
  padding-bottom: 4rem;
}

.py-12 {
  padding-top: 3rem;
  padding-bottom: 3rem;
}

.w-16 {
  width: 4rem;
}

.h-16 {
  height: 4rem;
}

@media (min-width: 768px) {
  .md\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>
