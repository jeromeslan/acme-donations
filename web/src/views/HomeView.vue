<template>
  <div class="page-layout">
    <AppNavbar />
    
    <main class="page-content">
      <!-- Hero Section -->
      <section class="bg-gradient-to-r from-primary-600 to-primary-700 text-white py-20">
        <div class="container text-center">
          <h1 class="text-4xl font-bold mb-6">Make a Difference Together</h1>
          <p class="text-xl mb-8 max-w-2xl mx-auto">
            Join our community in creating positive impact through charitable campaigns that matter
          </p>
          
          <!-- Stats -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            <div class="stat-item">
              <div class="stat-value text-3xl font-bold">{{ formatCurrency(globalStats.totalRaised) }}</div>
              <div class="stat-label text-primary-100">Total Raised</div>
            </div>
            <div class="stat-item">
              <div class="stat-value text-3xl font-bold">{{ globalStats.totalCampaigns }}</div>
              <div class="stat-label text-primary-100">Active Campaigns</div>
            </div>
            <div class="stat-item">
              <div class="stat-value text-3xl font-bold">{{ globalStats.totalDonations }}</div>
              <div class="stat-label text-primary-100">Donations Made</div>
            </div>
          </div>

          <div v-if="!isAdmin" class="mt-12">
            <BaseButton
              tag="router-link"
              to="/create-campaign"
              variant="secondary"
              size="lg"
              class="bg-white text-primary-600 hover:bg-gray-50"
            >
              Start a Campaign
            </BaseButton>
          </div>
        </div>
      </section>

      <!-- Featured Campaigns -->
      <section v-if="featuredCampaigns.length > 0" class="py-16">
        <div class="container">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Campaigns</h2>
            <p class="text-lg text-gray-600">Discover campaigns making the biggest impact</p>
          </div>

          <div class="campaign-grid">
            <CampaignCard
              v-for="campaign in featuredCampaigns"
              :key="campaign.id"
              :campaign="campaign"
            />
          </div>
        </div>
      </section>

      <!-- All Campaigns -->
      <section class="py-16 bg-gray-50">
        <div class="container">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
              {{ isAdmin ? 'All Campaigns' : 'Active Campaigns' }}
            </h2>
            <p class="text-lg text-gray-600">Browse all available campaigns</p>
          </div>

          <div v-if="loading" class="text-center py-12">
            <div class="loading"></div>
            <p class="mt-4 text-gray-600">Loading campaigns...</p>
          </div>

          <div v-else-if="campaigns.length === 0" class="text-center py-12">
            <div class="text-gray-400 mb-4">
              <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z" clip-rule="evenodd" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No campaigns found</h3>
            <p class="text-gray-600 mb-6">Be the first to create a campaign!</p>
            <BaseButton
              v-if="!isAdmin"
              tag="router-link"
              to="/create-campaign"
              variant="primary"
            >
              Create Campaign
            </BaseButton>
          </div>

          <div v-else class="campaign-grid">
            <CampaignCard
              v-for="campaign in campaigns"
              :key="campaign.id"
              :campaign="campaign"
            />
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/api/client'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import CampaignCard from '@/components/CampaignCard.vue'

const auth = useAuthStore()
const loading = ref(false)
const campaigns = ref([])
const featuredCampaigns = ref([])
const globalStats = ref({
  totalRaised: 0,
  totalCampaigns: 0,
  totalDonations: 0
})

const isAdmin = computed(() => {
  return auth.user?.roles?.includes('admin') ?? false
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const fetchCampaigns = async () => {
  loading.value = true
  try {
    const response = await api.get('/api/campaigns')
    campaigns.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching campaigns:', error)
    campaigns.value = []
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

onMounted(async () => {
  await Promise.all([
    fetchCampaigns(),
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
  .md\\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}
</style>