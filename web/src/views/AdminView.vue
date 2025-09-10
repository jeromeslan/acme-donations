<template>
  <main class="p-6 space-y-6">
    <h1 class="text-2xl font-bold">Admin dashboard</h1>

    <section v-if="kpis" class="grid md:grid-cols-2 gap-4">
      <div class="border rounded p-3">
        <h2 class="font-semibold mb-2">Donations</h2>
        <div class="text-sm">Count: {{ kpis.donations.count }}</div>
        <div class="text-sm">Sum: {{ kpis.donations.sum }}</div>
        <div class="text-sm">Unique donors: {{ kpis.donations.unique_donors }}</div>
      </div>
      <div class="border rounded p-3">
        <h2 class="font-semibold mb-2">Top campaigns</h2>
        <ul class="space-y-1">
          <li v-for="c in kpis.top_campaigns" :key="c.id">
            <RouterLink class="text-sky-700 underline" :to="{ name: 'campaign-detail', params: { id: c.id } }">
              {{ c.title }}
            </RouterLink>
            <span class="text-slate-600"> — {{ c.donated_amount }}</span>
          </li>
        </ul>
      </div>
    </section>

    <!-- Latest campaigns table -->
    <section class="border rounded p-4 space-y-4">
      <h2 class="font-semibold text-lg">Dernières campagnes</h2>
      <div class="flex flex-wrap items-end gap-3">
        <div>
          <label class="block text-xs mb-1">Recherche</label>
          <input v-model="filters.q" type="text" class="border rounded px-2 py-1" placeholder="titre..." @keyup.enter="fetchCampaigns(1)">
        </div>
        <div>
          <label class="block text-xs mb-1">Statut</label>
          <select v-model="filters.status" class="border rounded px-2 py-1" @change="fetchCampaigns(1)">
            <option value="">Tous</option>
            <option value="draft">draft</option>
            <option value="pending">pending</option>
            <option value="active">active</option>
            <option value="completed">completed</option>
            <option value="archived">archived</option>
          </select>
        </div>
        <button class="ml-auto bg-sky-600 text-white rounded px-3 py-1" @click="fetchCampaigns(1)">Filtrer</button>
      </div>

      <div class="overflow-auto">
        <table class="w-full text-sm border-collapse">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2 pr-3">Titre</th>
              <th class="py-2 pr-3">Catégorie</th>
              <th class="py-2 pr-3">Statut</th>
              <th class="py-2 pr-3">Objectif</th>
              <th class="py-2 pr-3">Collecté</th>
              <th class="py-2 pr-3">Publié</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in latest" :key="c.id" class="border-b hover:bg-slate-50">
              <td class="py-2 pr-3">
                <RouterLink class="text-sky-700 underline" :to="{ name: 'campaign-detail', params: { id: c.id } }">{{ c.title }}</RouterLink>
              </td>
              <td class="py-2 pr-3">{{ c.category?.name || '-' }}</td>
              <td class="py-2 pr-3">{{ c.status }}</td>
              <td class="py-2 pr-3">{{ c.goal_amount }}</td>
              <td class="py-2 pr-3">{{ c.donated_amount }}</td>
              <td class="py-2 pr-3">{{ c.published_at ? new Date(c.published_at).toLocaleDateString() : '-' }}</td>
            </tr>
            <tr v-if="!loading && latest.length === 0">
              <td colspan="6" class="py-4 text-center text-slate-500">Aucune campagne</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex items-center justify-between">
        <div class="text-xs text-slate-600">Page {{ page }} / {{ lastPage }}</div>
        <div class="space-x-2">
          <button class="border rounded px-3 py-1" :disabled="page<=1 || loading" @click="fetchCampaigns(page-1)">Précédent</button>
          <button class="border rounded px-3 py-1" :disabled="page>=lastPage || loading" @click="fetchCampaigns(page+1)">Suivant</button>
        </div>
      </div>
      <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    </section>
  </main>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '@/api/client'

const kpis = ref<any>(null)
const latest = ref<any[]>([])
const page = ref(1)
const lastPage = ref(1)
const loading = ref(false)
const error = ref('')
const filters = ref<{ q: string; status: string }>({ q: '', status: '' })

onMounted(async () => {
  const { data } = await api.get('/api/admin/kpis')
  kpis.value = data
  await fetchCampaigns(1)
})

async function fetchCampaigns(p: number) {
  loading.value = true
  error.value = ''
  try {
    page.value = p
    const { data } = await api.get('/api/campaigns', { params: { q: filters.value.q, status: filters.value.status, page: page.value, per_page: 10 } })
    // Support both paginated (data/meta) and array
    if (Array.isArray(data)) {
      latest.value = data
      lastPage.value = p
    } else {
      latest.value = data.data
      lastPage.value = data.meta?.last_page ?? p
    }
  } finally {
    loading.value = false
  }
  if (!Array.isArray(latest.value)) {
    latest.value = []
  }
}
</script>


