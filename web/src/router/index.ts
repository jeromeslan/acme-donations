import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
  },
  {
    path: '/campaign/:id',
    name: 'campaign-detail',
    component: () => import('../views/CampaignDetailView.vue'),
    props: true,
  },
  {
    path: '/admin',
    name: 'admin',
    meta: { roles: ['admin'] },
    component: () => import('../views/AdminView.vue'),
  },
  {
    path: '/login-demo',
    name: 'login-demo',
    component: () => import('../views/LoginDemoView.vue'),
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
  const roles = (to.meta?.roles as string[] | undefined) ?? []
  if (!roles.length) return true
  const auth = useAuthStore()
  if (!auth.user) {
    try { await auth.fetchUser() } catch {}
  }
  if (!auth.user) return { name: 'login' }
  const hasRole = roles.some(r => auth.user?.roles?.includes(r))
  return hasRole ? true : { name: 'login' }
})

export default router


