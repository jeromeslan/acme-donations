<template>
  <div class="home-view">
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <h1>Ensemble, créons un impact positif</h1>
          <p>Rejoignez votre communauté dans des initiatives solidaires qui font la différence</p>
          <div class="hero-stats">
          <div class="stat-item">
            <div class="stat-value">{{ $filters.formatCurrency(globalStats.totalRaised) }}</div>
            <div class="stat-label">Collecté</div>
          </div>
            <div class="stat-item">
              <div class="stat-value">{{ globalStats.activeCampaigns }}</div>
              <div class="stat-label">Campagnes actives</div>
            </div>
            <div class="stat-item">
              <div class="stat-value">{{ globalStats.totalUsers }}</div>
              <div class="stat-label">Participants</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Campaigns Section -->
    <section class="featured-campaigns">
      <div class="container">
        <h2>Campagnes à la une</h2>
        <div class="campaigns-grid" v-if="featuredCampaigns.length > 0">
          <div
            v-for="campaign in featuredCampaigns"
            :key="campaign.id"
            class="campaign-card featured"
            @click="goToCampaign(campaign.id)"
          >
            <div class="campaign-image">
              <img :src="getCampaignImage(campaign)" :alt="campaign.title" />
              <div class="campaign-status">{{ getStatusLabel(campaign.status) }}</div>
            </div>
            <div class="campaign-content">
              <h3>{{ campaign.title }}</h3>
              <p>{{ campaign.description }}</p>
              <div class="campaign-meta">
                <span class="category">{{ campaign.category?.name }}</span>
                <span class="goal">{{ campaign.donated_amount }} }} / {{ campaign.goal_amount }} }}</span>
              </div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: getProgressPercentage(campaign) + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="no-campaigns">
          <p>Aucune campagne à la une pour le moment</p>
        </div>
      </div>
    </section>

    <!-- Active Campaigns Section -->
    <section class="active-campaigns">
      <div class="container">
        <div class="section-header">
          <h2>Campagnes en cours</h2>
          <router-link v-if="!isAdmin" to="/create-campaign" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer une campagne
          </router-link>
        </div>

        <div class="campaigns-grid" v-if="activeCampaigns.length > 0">
          <div
            v-for="campaign in activeCampaigns"
            :key="campaign.id"
            class="campaign-card"
            @click="goToCampaign(campaign.id)"
          >
            <div class="campaign-image">
              <img :src="getCampaignImage(campaign)" :alt="campaign.title" />
              <div class="campaign-status">{{ getStatusLabel(campaign.status) }}</div>
            </div>
            <div class="campaign-content">
              <h3>{{ campaign.title }}</h3>
              <p>{{ campaign.description }}</p>
              <div class="campaign-meta">
                <span class="category">{{ campaign.category?.name }}</span>
                <span class="goal">{{ campaign.donated_amount }} }} / {{ campaign.goal_amount }} }}</span>
              </div>
              <div class="progress-bar">
                <div
                  class="progress-fill"
                  :style="{ width: getProgressPercentage(campaign) + '%' }"
                ></div>
              </div>
              <div class="campaign-author" v-if="!isAdmin && campaign.creator">
                Par {{ campaign.creator.name }}
              </div>
            </div>
          </div>
        </div>
        <div v-else class="no-campaigns">
          <p>Aucune campagne active pour le moment</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCampaignsStore } from '@/stores/campaigns'
import type { Campaign } from '@/stores/campaigns'

const router = useRouter()
const auth = useAuthStore()
const campaignsStore = useCampaignsStore()

const globalStats = ref({
  totalRaised: 0,
  activeCampaigns: 0,
  totalUsers: 0
})

const featuredCampaigns = ref<Campaign[]>([])
const activeCampaigns = ref<Campaign[]>([])

const isAdmin = computed(() => {
  return auth.user?.roles?.includes('admin') || false
})

const goToCampaign = (campaignId: number) => {
  router.push(`/campaign/${campaignId}`)
}

const getCampaignImage = (campaign: Campaign) => {
  // Placeholder image - à remplacer par l'image réelle de la campagne
  return `https://via.placeholder.com/400x250/4F46E5/FFFFFF?text=${encodeURIComponent(campaign.title)}`
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    draft: 'Brouillon',
    pending: 'En attente',
    active: 'Active',
    completed: 'Terminée',
    rejected: 'Rejetée'
  }
  return labels[status] || status
}

const getProgressPercentage = (campaign: Campaign) => {
  if (campaign.goal_amount === 0) return 0
  return Math.min((campaign.donated_amount / campaign.goal_amount) * 100, 100)
}

const loadCampaigns = async () => {
  try {
    // Charger les statistiques globales (uniquement pour les admins)
    if (isAdmin.value) {
      const statsResponse = await fetch('/api/admin/kpis', {
        headers: {
          'Authorization': `Bearer ${auth.user?.token}`,
          'Accept': 'application/json'
        }
      })

      if (statsResponse.ok) {
        globalStats.value = await statsResponse.json()
      }
    } else {
      // Pour les utilisateurs normaux, charger des stats basiques
      globalStats.value = {
        totalRaised: 0,
        activeCampaigns: 0,
        totalUsers: 0
      }
    }

    // Charger les campagnes featured
    await campaignsStore.fetchFeatured()
    featuredCampaigns.value = campaignsStore.featured

    // Charger les campagnes selon le rôle
    const params: any = {}
    if (!isAdmin.value) {
      // Les utilisateurs normaux voient uniquement les campagnes publiées
      params.status = 'published'
    } else {
      // Les admins voient les campagnes actives
      params.status = 'published'
    }

    await campaignsStore.fetch(params)
    activeCampaigns.value = campaignsStore.items

  } catch (error) {
    console.error('Erreur lors du chargement des campagnes:', error)
  }
}

onMounted(() => {
  loadCampaigns()
})
</script>

<style scoped>
.home-view {
  min-height: 100vh;
}

/* Hero Section */
.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 4rem 0;
  margin-bottom: 3rem;
}

.hero-content {
  text-align: center;
  max-width: 800px;
  margin: 0 auto;
}

.hero-content h1 {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.hero-content p {
  font-size: 1.25rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 2rem;
  font-weight: 700;
  display: block;
}

.stat-label {
  font-size: 0.875rem;
  opacity: 0.8;
}

/* Sections */
.featured-campaigns,
.active-campaigns {
  padding: 3rem 0;
}

.featured-campaigns {
  background-color: #f8fafc;
}

.active-campaigns {
  background-color: white;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.section-header h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

/* Campaigns Grid */
.campaigns-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 2rem;
}

.campaign-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.campaign-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
}

.campaign-card.featured {
  border-color: #4f46e5;
  position: relative;
}

.campaign-card.featured::before {
  content: '⭐';
  position: absolute;
  top: 12px;
  right: 12px;
  background: #4f46e5;
  color: white;
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 0.75rem;
  z-index: 10;
}

.campaign-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.campaign-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.campaign-card:hover .campaign-image img {
  transform: scale(1.05);
}

.campaign-status {
  position: absolute;
  top: 12px;
  left: 12px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.campaign-content {
  padding: 1.5rem;
}

.campaign-content h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.campaign-content p {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
  margin-bottom: 1rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.campaign-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.category {
  background: #e5e7eb;
  color: #374151;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
}

.goal {
  font-size: 0.875rem;
  font-weight: 600;
  color: #059669;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background-color: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 1rem;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #059669 0%, #10b981 100%);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.campaign-author {
  font-size: 0.75rem;
  color: #9ca3af;
  font-style: italic;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: #4f46e5;
  color: white;
}

.btn-primary:hover {
  background: #4338ca;
  transform: translateY(-1px);
}

/* No campaigns state */
.no-campaigns {
  text-align: center;
  padding: 3rem;
  color: #6b7280;
}

.no-campaigns p {
  font-size: 1.125rem;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2rem;
  }

  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .campaigns-grid {
    grid-template-columns: 1fr;
  }

  .campaign-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}
</style>


