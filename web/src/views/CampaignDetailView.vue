<template>
  <main class="p-6 space-y-4" v-if="campaign">
    <h1 class="text-2xl font-bold">{{ campaign.title }}</h1>
    <p class="text-slate-700">{{ campaign.description }}</p>
    <div class="text-sm text-slate-600">Status: {{ campaign.status }} | Donated: {{ campaign.donated_amount }} / {{ campaign.goal_amount }}</div>

    <div class="flex items-center gap-2">
      <input v-model.number="amount" type="number" min="1" class="border rounded px-2 py-1 w-32" />
      <button class="px-3 py-1 bg-sky-600 text-white rounded" @click="donate">Donner</button>
    </div>
  </main>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useCampaignsStore } from '@/stores/campaigns'

const route = useRoute()
const campaigns = useCampaignsStore()
const campaign = ref<any>(null)
const amount = ref<number>(25)

onMounted(async () => {
  const id = Number(route.params.id)
  campaign.value = await campaigns.getOne(id)
})

async function donate() {
  if (!campaign.value) return
  await campaigns.donate(campaign.value.id, amount.value)
  // naive feedback
  alert('Donation envoyée (mock).')
}
</script>


