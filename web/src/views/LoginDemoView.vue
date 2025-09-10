<template>
  <main class="p-6">
    <p>Logging in...</p>
  </main>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { initCsrf } from '@/api/client'

const router = useRouter()
const auth = useAuthStore()

onMounted(async () => {
  try {
    await initCsrf()
    await auth.login('admin@acme.test', 'password')
    router.replace({ name: 'admin' })
  } catch (e) {
    router.replace({ name: 'home' })
  }
})
</script>


