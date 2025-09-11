import axios from 'axios'

// Use relative base to leverage Vite dev proxy (to api-nginx) and work in prod
export const api = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
})

export async function initCsrf(): Promise<void> {
  try {
    await api.get('/sanctum/csrf-cookie', {
      headers: {
        'Accept': 'application/json',
      }
    })
    console.log('CSRF token initialized successfully')
  } catch (error) {
    console.error('Failed to initialize CSRF token:', error)
    throw error
  }
}

// Initialize CSRF token on module load
let csrfInitialized = false
let lastCsrfInit = 0
const CSRF_TOKEN_LIFETIME = 15 * 60 * 1000 // 15 minutes

export async function ensureCsrfToken(): Promise<void> {
  const now = Date.now()
  if (!csrfInitialized || (now - lastCsrfInit) > CSRF_TOKEN_LIFETIME) {
    console.log('Initializing/refresing CSRF token...')
    await initCsrf()
    csrfInitialized = true
    lastCsrfInit = now
  }
}

// Force refresh CSRF token
export async function refreshCsrfToken(): Promise<void> {
  console.log('Force refreshing CSRF token...')
  csrfInitialized = false
  await ensureCsrfToken()
}

// Intercepteur pour gérer automatiquement les tokens CSRF et erreurs d'auth
api.interceptors.request.use(
  async (config) => {
    // Pour les requêtes POST/PUT/DELETE/PATCH, s'assurer que le token CSRF est disponible
    if (['post', 'put', 'delete', 'patch'].includes(config.method?.toLowerCase() || '')) {
      try {
        await ensureCsrfToken()
        console.log(`CSRF token ensured for ${config.method?.toUpperCase()} ${config.url}`)
      } catch (error) {
        console.warn('Failed to ensure CSRF token:', error)
      }
    }

    // Ajouter le token CSRF dans les headers si disponible
    const xsrfToken = getCookie('XSRF-TOKEN')
    if (xsrfToken) {
      config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken)
    }

    return config
  },
  (error) => Promise.reject(error)
)

// Fonction helper pour récupérer les cookies
function getCookie(name: string): string | null {
  const value = `; ${document.cookie}`
  const parts = value.split(`; ${name}=`)
  if (parts.length === 2) return parts.pop()?.split(';').shift() || null
  return null
}

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const status = error?.response?.status
    const originalRequest = error.config

    console.log(`API Error: ${status} for ${originalRequest?.method?.toUpperCase()} ${originalRequest?.url}`)

    if (status === 419 && !originalRequest._retry) {
      // Token CSRF expiré, on essaie de le renouveler
      originalRequest._retry = true
      try {
        console.log('CSRF token expired, refreshing...')
        csrfInitialized = false // Reset flag to force re-initialization
        lastCsrfInit = 0 // Reset timestamp
        await initCsrf()
        console.log('Retrying request after CSRF refresh...')
        return api(originalRequest)
      } catch (refreshError) {
        console.error('Failed to refresh CSRF token:', refreshError)
        return Promise.reject(error)
      }
    }

    if (status === 401) {
      console.log('Unauthorized request - user needs to login')
      // Le store d'auth peut gérer la redirection
    }

    if (status === 422) {
      console.log('Validation error:', error?.response?.data?.errors)
    }

    return Promise.reject(error)
  }
)

// Fonction de diagnostic CSRF
export async function diagnoseCsrf(): Promise<void> {
  console.log('=== CSRF Diagnostic ===')
  console.log('CSRF initialized:', csrfInitialized)
  console.log('Last CSRF init:', new Date(lastCsrfInit).toLocaleString())
  console.log('Time since last init:', Date.now() - lastCsrfInit, 'ms')
  console.log('Cookies:', document.cookie)

  // Vérifier le token XSRF dans les cookies
  const xsrfCookie = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))
  console.log('XSRF cookie found:', !!xsrfCookie)
  if (xsrfCookie) {
    console.log('XSRF cookie value:', xsrfCookie.substring(12, 20) + '...')
  }

  try {
    const response = await api.get('/sanctum/csrf-cookie')
    console.log('CSRF cookie response:', response.status)
    console.log('Response headers:', response.headers)
  } catch (error: any) {
    console.error('CSRF cookie fetch failed:', error?.response?.status, error?.message)
  }
}

// Fonction de test pour créer une campagne
export async function testCreateCampaign(): Promise<void> {
  console.log('=== Test Create Campaign ===')

  try {
    await ensureCsrfToken()
    console.log('CSRF token ensured for test')

    const testData = {
      title: 'Test Campaign',
      description: 'This is a test campaign',
      goal_amount: 100,
      category_id: 1,
      featured: false,
      status: 'draft'
    }

    console.log('Sending test data:', testData)
    const response = await api.post('/api/campaigns', testData)
    console.log('Test campaign created successfully:', response.data)
    return response.data
  } catch (error: any) {
    console.error('Test campaign creation failed:', error?.response?.data || error?.message)
    throw error
  }
}



