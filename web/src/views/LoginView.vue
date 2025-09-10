<template>
  <main class="min-h-screen flex items-center justify-center p-6">
    <form class="w-full max-w-sm border rounded p-6 space-y-4" @submit.prevent="onSubmit">
      <h1 class="text-2xl font-bold">Connexion</h1>
      <div>
        <label class="block text-sm mb-1">Email</label>
        <input v-model="email" type="email" required class="w-full border rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm mb-1">Mot de passe</label>
        <input v-model="password" type="password" required class="w-full border rounded px-3 py-2" />
      </div>
      <button type="submit" class="w-full bg-sky-600 text-white rounded px-3 py-2">Se connecter</button>
      <button type="button" class="w-full border rounded px-3 py-2" @click="quickLogin">Login demo</button>
      <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
    </form>
  </main>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref('')
const auth = useAuthStore()
const router = useRouter()

async function onSubmit() {
  error.value = ''
  try {
    await auth.login(email.value, password.value)
    await afterLogin()
  } catch (e: any) {
    console.error('Login error:', e)
    if (e.response?.status === 422) {
      error.value = 'Format d\'email invalide'
    } else if (e.response?.status === 401) {
      error.value = 'Identifiants invalides'
    } else if (e.response?.status === 419) {
      error.value = 'Session expirée, veuillez réessayer'
    } else {
      error.value = 'Erreur de connexion'
    }
  }
}

async function quickLogin() {
  error.value = ''
  try {
    await auth.login('admin@acme.test', 'password')
    await afterLogin()
  } catch (e: any) {
    console.error('Quick login error:', e)
    error.value = 'Login demo indisponible'
  }
}

async function afterLogin() {
  const roles = auth.user?.roles ?? []
  if (roles.includes('admin')) {
    router.replace({ name: 'admin' })
  } else {
    router.replace({ name: 'campaigns' })
  }
}
</script>


