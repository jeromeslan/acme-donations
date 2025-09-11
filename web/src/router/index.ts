import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
  },
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/campaign/:id',
    name: 'campaign-detail',
    component: () => import('../views/CampaignDetailView.vue'),
    props: true,
    meta: { requiresAuth: true },
  },
  {
    path: '/create-campaign',
    name: 'create-campaign',
    component: () => import('../views/CreateCampaignView.vue'),
    meta: { requiresAuth: true },
  },
  {
    path: '/admin',
    name: 'admin',
    meta: { roles: ['admin'] },
    component: () => import('../views/AdminView.vue'),
  },
  {
    path: '/creator',
    name: 'creator',
    meta: { roles: ['creator'] },
    component: () => import('../views/CreatorView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Vérifier l'authentification si requise
  if (to.meta?.requiresAuth) {
    if (!auth.user) {
      try {
        await auth.fetchUser()
      } catch {}
    }
    if (!auth.user) return { name: 'login' }
  }

  // Vérifier les rôles spécifiques
  const roles = (to.meta?.roles as string[] | undefined) ?? []
  if (roles.length) {
    if (!auth.user) return { name: 'login' }
    const hasRole = roles.some(r => auth.user?.roles?.includes(r))
    return hasRole ? true : { name: 'login' }
  }

  return true
})

export default router


