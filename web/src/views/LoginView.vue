<template>
  <div class="auth-layout">
    <div class="auth-card">
      <BaseCard>
        <template #header>
          <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
            <p class="text-gray-600">Sign in to your ACME Donations account</p>
          </div>
        </template>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          <BaseInput
            v-model="form.email"
            label="Email Address"
            type="email"
            placeholder="Enter your email"
            required
            :error="errors.email"
          />

          <BaseInput
            v-model="form.password"
            label="Password"
            type="password"
            placeholder="Enter your password"
            required
            :error="errors.password"
          />

          <BaseAlert v-if="errors.general" variant="error" dismissible>
            {{ errors.general }}
          </BaseAlert>

          <BaseButton
            type="submit"
            variant="primary"
            size="lg"
            :loading="loading"
            class="w-full"
          >
            {{ loading ? 'Signing In...' : 'Sign In' }}
          </BaseButton>
        </form>

        <template #footer>
          <div class="text-center">
            <p class="text-sm text-gray-600">
              Demo credentials: <strong>admin@acme.test</strong> / <strong>password</strong>
            </p>
          </div>
        </template>
      </BaseCard>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import BaseCard from '@/components/ui/BaseCard.vue'
import BaseInput from '@/components/ui/BaseInput.vue'
import BaseButton from '@/components/ui/BaseButton.vue'
import BaseAlert from '@/components/ui/BaseAlert.vue'

const auth = useAuthStore()
const router = useRouter()
const loading = ref(false)

const form = reactive({
  email: '',
  password: ''
})

const errors = reactive({
  email: '',
  password: '',
  general: ''
})

const clearErrors = () => {
  errors.email = ''
  errors.password = ''
  errors.general = ''
}

const handleSubmit = async () => {
  clearErrors()
  loading.value = true

  try {
    await auth.login(form.email, form.password)
    await navigateAfterLogin()
  } catch (error: any) {
    console.error('Login error:', error)
    
    if (error.response?.status === 422) {
      const validationErrors = error.response.data.errors || {}
      errors.email = validationErrors.email?.[0] || ''
      errors.password = validationErrors.password?.[0] || ''
      if (!errors.email && !errors.password) {
        errors.general = 'Invalid email format'
      }
    } else if (error.response?.status === 401) {
      errors.general = 'Invalid credentials. Please check your email and password.'
    } else if (error.response?.status === 419) {
      errors.general = 'Session expired. Please try again.'
    } else {
      errors.general = 'Connection error. Please try again later.'
    }
  } finally {
    loading.value = false
  }
}

const navigateAfterLogin = async () => {
  const roles = auth.user?.roles ?? []
  if (roles.includes('admin')) {
    router.replace({ name: 'admin' })
  } else {
    router.replace({ name: 'home' })
  }
}
</script>