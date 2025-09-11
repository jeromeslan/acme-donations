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
          <li class="user-menu-container">
            <button 
              ref="userMenuButton"
              @click="toggleUserMenu" 
              class="user-menu-trigger"
              :class="{ 'active': showUserMenu }"
            >
              <div class="user-avatar">
                {{ userInitials }}
              </div>
              <span class="user-name">{{ auth.user.name || auth.user.email }}</span>
              <svg class="dropdown-arrow" :class="{ 'rotated': showUserMenu }" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <!-- User Dropdown -->
            <div v-if="showUserMenu" class="user-dropdown" @click.stop>
              <div class="dropdown-header">
                <div class="user-info">
                  <div class="user-avatar-large">
                    {{ userInitials }}
                  </div>
                  <div class="user-details">
                    <div class="user-name-large">{{ auth.user.name || 'User' }}</div>
                    <div class="user-email">{{ auth.user.email }}</div>
                  </div>
                </div>
              </div>
              <div class="dropdown-actions">
                <button @click="handleLogout" class="logout-button">
                  <svg class="logout-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
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

const userMenuButton = ref<HTMLElement | null>(null)

const toggleUserMenu = (event: Event) => {
  event.stopPropagation()
  showUserMenu.value = !showUserMenu.value
}

const closeUserMenu = () => {
  showUserMenu.value = false
}

const handleLogout = async () => {
  closeUserMenu()
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
  if (userMenuButton.value && !userMenuButton.value.contains(target) && showUserMenu.value) {
    closeUserMenu()
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
/* User Menu Container */
.user-menu-container {
  position: relative;
  display: inline-block;
}

/* User Menu Trigger Button */
.user-menu-trigger {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border: none;
  background: transparent;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 500;
}

.user-menu-trigger:hover {
  background-color: #f3f4f6;
  color: #111827;
}

.user-menu-trigger.active {
  background-color: #eff6ff;
  color: #2563eb;
}

/* User Avatar */
.user-avatar {
  width: 2rem;
  height: 2rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
}

.user-avatar-large {
  width: 2.5rem;
  height: 2.5rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 600;
  flex-shrink: 0;
}

/* User Name */
.user-name {
  display: none;
  max-width: 120px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

@media (min-width: 768px) {
  .user-name {
    display: block;
  }
}

/* Dropdown Arrow */
.dropdown-arrow {
  width: 1rem;
  height: 1rem;
  transition: transform 0.2s ease;
  flex-shrink: 0;
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

/* User Dropdown */
.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  width: 280px;
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border: 1px solid #e5e7eb;
  z-index: 50;
  overflow: hidden;
  animation: dropdownFadeIn 0.15s ease-out;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Dropdown Header */
.dropdown-header {
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.user-details {
  flex: 1;
  min-width: 0;
}

.user-name-large {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.user-email {
  font-size: 0.75rem;
  color: #6b7280;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin-top: 0.125rem;
}

/* Dropdown Actions */
.dropdown-actions {
  padding: 0.5rem;
}

.logout-button {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem;
  border: none;
  background: transparent;
  color: #dc2626;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.logout-button:hover {
  background-color: #fef2f2;
  color: #b91c1c;
}

.logout-icon {
  width: 1rem;
  height: 1rem;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 767px) {
  .user-dropdown {
    width: 260px;
    right: -1rem;
  }
}

/* Utilities */
.w-8 { width: 2rem; }
.h-8 { height: 2rem; }
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.rotate-180 { transform: rotate(180deg); }
.z-50 { z-index: 50; }
</style>

