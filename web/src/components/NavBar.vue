<template>
  <nav class="flex items-center justify-between p-4 border-b">
    <div class="flex items-center gap-3">
      <span class="font-bold">ACME Donations</span>
      <RouterLink class="text-sky-600" to="/">Accueil</RouterLink>
      <RouterLink class="text-sky-600" to="/creator">Créateur</RouterLink>
      <RouterLink class="text-sky-600" to="/admin">Admin</RouterLink>
    </div>
    <div class="flex items-center gap-2">
      <RouterLink v-if="!auth.user" class="px-3 py-1 bg-sky-600 text-white rounded" :to="{ name: 'login-demo' }">Login demo</RouterLink>
      <div v-else class="flex items-center gap-2">
        <span class="text-sm">{{ auth.user.name }}</span>
        <button class="px-3 py-1 border rounded" @click="logout">Logout</button>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { initCsrf } from '@/api/client'
import { RouterLink, useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

async function quickLogin() {
  try {
    await initCsrf()
    await auth.login('admin@acme.test', 'password')
    await router.push({ name: 'admin' })
  } catch (e: any) {
    alert('Login failed')
    // optional: console.error(e)
  }
}

async function logout() {
  try {
    await auth.logout()
  } catch {}
}
</script>


