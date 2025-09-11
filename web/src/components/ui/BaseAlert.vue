<template>
  <div v-if="show" :class="alertClasses" class="alert">
    <div class="flex items-center">
      <div class="flex-shrink-0">
        <svg v-if="type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <svg v-else-if="type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <svg v-else-if="type === 'warning'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium">{{ message }}</p>
      </div>
      <div v-if="dismissible" class="ml-auto pl-3">
        <div class="-mx-1.5 -my-1.5">
          <button @click="dismiss" class="inline-flex p-1.5 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 hover:opacity-75">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'

interface Props {
  type?: 'success' | 'error' | 'warning' | 'info'
  message: string
  show?: boolean
  dismissible?: boolean
  autoHide?: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  show: true,
  dismissible: true,
  autoHide: 5000
})

const emit = defineEmits<{
  dismiss: []
}>()

const show = ref(props.show)

const alertClasses = computed(() => {
  const baseClasses = 'rounded-md p-4 mb-4'
  const typeClasses = {
    success: 'bg-green-50 text-green-800 border border-green-200',
    error: 'bg-red-50 text-red-800 border border-red-200',
    warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
    info: 'bg-blue-50 text-blue-800 border border-blue-200'
  }
  
  return `${baseClasses} ${typeClasses[props.type]}`
})

const dismiss = () => {
  show.value = false
  emit('dismiss')
}

// Auto-hide functionality
let timeout: NodeJS.Timeout | null = null

watch(() => props.show, (newShow) => {
  show.value = newShow
  if (newShow && props.autoHide > 0) {
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => {
      dismiss()
    }, props.autoHide)
  }
}, { immediate: true })

// Cleanup timeout on unmount
import { onUnmounted } from 'vue'
onUnmounted(() => {
  if (timeout) clearTimeout(timeout)
})
</script>

<style scoped>
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.ml-3 { margin-left: 0.75rem; }
.ml-auto { margin-left: auto; }
.pl-3 { padding-left: 0.75rem; }
.p-4 { padding: 1rem; }
.p-1\.5 { padding: 0.375rem; }
.mb-4 { margin-bottom: 1rem; }
.-mx-1\.5 { margin-left: -0.375rem; margin-right: -0.375rem; }
.-my-1\.5 { margin-top: -0.375rem; margin-bottom: -0.375rem; }
.flex-shrink-0 { flex-shrink: 0; }
.rounded-md { border-radius: 0.375rem; }
.inline-flex { display: inline-flex; }
.focus\:outline-none:focus { outline: 2px solid transparent; outline-offset: 2px; }
.focus\:ring-2:focus { box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); }
.focus\:ring-offset-2:focus { box-shadow: 0 0 0 2px #fff, 0 0 0 4px rgba(59, 130, 246, 0.5); }
.transition-colors { transition-property: color, background-color, border-color; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 200ms; }
.duration-200 { transition-duration: 200ms; }
.hover\:opacity-75:hover { opacity: 0.75; }

.alert {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>