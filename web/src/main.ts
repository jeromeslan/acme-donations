import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { ensureCsrfToken } from '@/api/client'
import './style.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)

// Filtre global pour formater la monnaie
app.config.globalProperties.$filters = {
  formatCurrency(value: number): string {
    return new Intl.NumberFormat('fr-FR', {
      style: 'currency',
      currency: 'EUR'
    }).format(value)
  }
}

// Helper global pour les filtres
declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $filters: {
      formatCurrency: (value: number) => string
    }
  }
}

// Initialize CSRF token on app startup
async function initApp() {
  try {
    await ensureCsrfToken()
    console.log('App initialized with CSRF token')
  } catch (error) {
    console.warn('Failed to initialize CSRF token on startup:', error)
  }

  app.mount('#app')
}

// Exposer les fonctions de diagnostic globalement pour le debugging
declare global {
  interface Window {
    diagnoseCsrf: () => Promise<void>
    testCreateCampaign: () => Promise<any>
    api: any
  }
}

// Start the app
initApp().then(() => {
  // Exposer les fonctions de diagnostic dans la console
  import('./api/client').then(({ diagnoseCsrf, testCreateCampaign, api }) => {
    window.diagnoseCsrf = diagnoseCsrf
    window.testCreateCampaign = testCreateCampaign
    window.api = api
    console.log('🔧 Diagnostic functions available:')
    console.log('- window.diagnoseCsrf()')
    console.log('- window.testCreateCampaign()')
    console.log('- window.api')
  })
})


