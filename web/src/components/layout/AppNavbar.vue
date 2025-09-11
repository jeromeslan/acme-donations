<template>
  <nav class="navbar">
    <div class="container">
      <div class="flex items-center justify-between py-4">
        <!-- Brand -->
        <router-link to="/" class="navbar-brand">
          ACME Donations
        </router-link>

        <!-- Navigation Links -->
        <ul v-if="auth.user" class="navbar-nav">
          <!-- User Navigation -->
          <li v-if="!isAdmin">
            <router-link to="/" class="navbar-link" active-class="active">
              Home
            </router-link>
          </li>
          <li v-if="!isAdmin">
            <router-link to="/create-campaign" class="navbar-link" active-class="active">
              Create Campaign
            </router-link>
          </li>
          
          <!-- Admin Navigation -->
          <li v-if="isAdmin">
            <router-link to="/admin" class="navbar-link" active-class="active">
              Dashboard
            </router-link>
          </li>
          <li v-if="isAdmin">
            <router-link to="/admin/campaigns" class="navbar-link" active-class="active">
              Campaigns
            </router-link>
          </li>

          <!-- User Menu -->
          <li class="relative">
            <button @click="toggleUserMenu" class="flex items-center gap-2 navbar-link">
              <div class="w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                {{ userInitials }}
              </div>
              <span class="hidden md:block">{{ auth.user.name }}</span>
              <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showUserMenu }" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <!-- User Dropdown -->
            <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
              <div class="py-1">
                <div class="px-4 py-2 text-sm text-gray-500 border-b border-gray-100">
                  {{ auth.user.email }}
                </div>
                <button @click="handleLogout" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                  Sign Out
                </button>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const router = useRouter()
const showUserMenu = ref(false)

const isAdmin = computed(() => {
  return auth.user?.roles?.includes('admin') ?? false
})

const userInitials = computed(() => {
  if (!auth.user?.name) return '?'
  return auth.user.name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = async () => {
  try {
    await auth.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// Close user menu when clicking outside
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.rotate-180 { transform: rotate(180deg); }
.z-50 { z-index: 50; }
</style>

