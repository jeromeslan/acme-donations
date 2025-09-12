<template>
  <div class="notification-bell" ref="bellRef">
    <button
      @click="toggleNotifications"
      class="bell-button"
      :class="{ 'has-unread': unreadCount > 0 }"
    >
      <svg class="bell-icon" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
      </svg>
      <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
    </button>

    <!-- Notifications Dropdown -->
    <Teleport to="body">
      <div 
        v-if="showNotifications && dropdownPosition" 
        class="notifications-dropdown"
        :style="dropdownPosition"
        @click.stop
      >
        <div class="dropdown-header">
          <h3 class="dropdown-title">Notifications</h3>
          <button 
            v-if="unreadCount > 0"
            @click="markAllAsRead"
            class="mark-all-read"
          >
            Mark all as read
          </button>
        </div>

        <div class="notifications-list">
          <div v-if="loading" class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading notifications...</p>
          </div>

          <div v-else-if="notifications.length === 0" class="empty-state">
            <svg class="empty-icon" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
            <p>No notifications yet</p>
          </div>

          <div v-else>
            <div
              v-for="notification in notifications"
              :key="notification.id"
              class="notification-item"
              :class="{ 'unread': !notification.read_at }"
              @click="markAsRead(notification)"
            >
              <div class="notification-content">
                <div class="notification-title">{{ notification.title }}</div>
                <div class="notification-message">{{ notification.message }}</div>
                <div class="notification-time">{{ formatTime(notification.created_at) }}</div>
              </div>
              <div v-if="!notification.read_at" class="unread-indicator"></div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from 'vue'
import { useToast } from 'vue-toastification'
import { api } from '@/api/client'

const toast = useToast()
const bellRef = ref<HTMLElement>()
const showNotifications = ref(false)
const loading = ref(false)
const notifications = ref([])
const unreadCount = ref(0)
const dropdownPosition = ref<Record<string, string> | null>(null)

const toggleNotifications = async () => {
  if (!showNotifications.value) {
    await loadNotifications()
    await updateDropdownPosition()
  }
  showNotifications.value = !showNotifications.value
}

const updateDropdownPosition = async () => {
  await nextTick()
  if (!bellRef.value) return

  const rect = bellRef.value.getBoundingClientRect()
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop
  const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft

  dropdownPosition.value = {
    position: 'absolute',
    top: `${rect.bottom + scrollTop + 8}px`,
    right: `${window.innerWidth - rect.right - scrollLeft}px`,
    zIndex: '9999'
  }
}

const loadNotifications = async () => {
  loading.value = true
  try {
    const response = await api.get('/api/me/notifications')
    notifications.value = response.data.notifications
    unreadCount.value = response.data.unread_count
  } catch (error) {
    console.error('Error loading notifications:', error)
    toast.error('Failed to load notifications')
  } finally {
    loading.value = false
  }
}

const markAsRead = async (notification: any) => {
  if (notification.read_at) return

  try {
    await api.post(`/api/notifications/${notification.id}/read`)
    notification.read_at = new Date().toISOString()
    unreadCount.value = Math.max(0, unreadCount.value - 1)
  } catch (error) {
    console.error('Error marking notification as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await api.post('/api/notifications/read-all')
    notifications.value.forEach((notification: any) => {
      if (!notification.read_at) {
        notification.read_at = new Date().toISOString()
      }
    })
    unreadCount.value = 0
    toast.success('All notifications marked as read')
  } catch (error) {
    console.error('Error marking all as read:', error)
    toast.error('Failed to mark all notifications as read')
  }
}

const formatTime = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffMs = now.getTime() - date.getTime()
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  const diffDays = Math.floor(diffHours / 24)

  if (diffHours < 1) return 'Just now'
  if (diffHours < 24) return `${diffHours}h ago`
  if (diffDays < 7) return `${diffDays}d ago`
  return date.toLocaleDateString()
}

const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (bellRef.value && !bellRef.value.contains(target) && showNotifications.value) {
    showNotifications.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  loadNotifications() // Load on mount to get unread count
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Expose method for parent to refresh notifications
defineExpose({
  loadNotifications
})
</script>

<style scoped>
.notification-bell {
  position: relative;
  display: inline-block;
}

.bell-button {
  position: relative;
  padding: 0.5rem;
  border: none;
  background: transparent;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #6b7280;
}

.bell-button:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.bell-button.has-unread {
  color: #3b82f6;
}

.bell-icon {
  width: 1.5rem;
  height: 1.5rem;
}

.notification-badge {
  position: absolute;
  top: 0.125rem;
  right: 0.125rem;
  background: #ef4444;
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  min-width: 1.25rem;
  height: 1.25rem;
  border-radius: 0.625rem;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.notifications-dropdown {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  border: 1px solid #e5e7eb;
  width: 380px;
  max-height: 500px;
  overflow: hidden;
  animation: dropdownFadeIn 0.15s ease-out;
}

@keyframes dropdownFadeIn {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-header {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
}

.dropdown-title {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.mark-all-read {
  background: none;
  border: none;
  color: #3b82f6;
  font-size: 0.875rem;
  cursor: pointer;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  transition: background-color 0.2s;
}

.mark-all-read:hover {
  background-color: #eff6ff;
}

.notifications-list {
  max-height: 400px;
  overflow-y: auto;
}

.loading-state, .empty-state {
  padding: 2rem;
  text-align: center;
  color: #6b7280;
}

.loading-spinner {
  width: 2rem;
  height: 2rem;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.empty-icon {
  width: 3rem;
  height: 3rem;
  margin: 0 auto 1rem;
  color: #d1d5db;
}

.notification-item {
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #f3f4f6;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
}

.notification-item:hover {
  background-color: #f9fafb;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item.unread {
  background-color: #eff6ff;
}

.notification-item.unread:hover {
  background-color: #dbeafe;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-weight: 600;
  color: #111827;
  margin-bottom: 0.25rem;
  font-size: 0.875rem;
}

.notification-message {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.4;
  margin-bottom: 0.5rem;
}

.notification-time {
  color: #9ca3af;
  font-size: 0.75rem;
}

.unread-indicator {
  width: 0.5rem;
  height: 0.5rem;
  background: #3b82f6;
  border-radius: 50%;
  margin-top: 0.125rem;
  flex-shrink: 0;
}

/* Responsive */
@media (max-width: 640px) {
  .notifications-dropdown {
    width: calc(100vw - 2rem);
    right: 1rem !important;
    left: 1rem !important;
  }
}
</style>
