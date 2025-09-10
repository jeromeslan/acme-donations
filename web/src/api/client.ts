import axios from 'axios'

// Use relative base to leverage Vite dev proxy (to api-nginx) and work in prod
export const api = axios.create({
  baseURL: '/',
  withCredentials: true,
})

export async function initCsrf(): Promise<void> {
  await api.get('/sanctum/csrf-cookie')
}

// Intercepteur pour gérer automatiquement les tokens CSRF et erreurs d'auth
api.interceptors.request.use(
  async (config) => {
    // Pour les requêtes POST/PUT/DELETE/PATCH, s'assurer que le token CSRF est disponible
    if (['post', 'put', 'delete', 'patch'].includes(config.method?.toLowerCase() || '')) {
      try {
        await initCsrf()
      } catch (error) {
        console.warn('Failed to initialize CSRF token:', error)
      }
    }
    return config
  },
  (error) => Promise.reject(error)
)

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const status = error?.response?.status
    const originalRequest = error.config

    if (status === 419 && !originalRequest._retry) {
      // Token CSRF expiré, on essaie de le renouveler une fois
      originalRequest._retry = true
      try {
        console.log('CSRF token expired, refreshing...')
        await initCsrf()
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

    return Promise.reject(error)
  }
)


