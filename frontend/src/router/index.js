import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import DashboardView from '../views/DashboardView.vue'
import JobsView from '../views/JobsView.vue'
import AlumniView from '../views/AlumniView.vue'
import MessagesView from '../views/MessagesView.vue'
import ResumesView from '../views/ResumesView.vue'
import AlumniManagementView from '../views/AlumniManagementView.vue'
import EventsView from '../views/EventsView.vue'
import MentorshipView from '../views/MentorshipView.vue'
import CommunitiesView from '../views/CommunitiesView.vue'
import AnalyticsView from '../views/AnalyticsView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

const router = createRouter({
	history: createWebHistory(),
	routes: [
		// Public routes
		{ path: '/login', name: 'login', component: LoginView, meta: { requiresGuest: true } },
		{ path: '/register', name: 'register', component: RegisterView, meta: { requiresGuest: true } },
		
		// Protected routes
		{ path: '/', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
		{ path: '/jobs', name: 'jobs', component: JobsView, meta: { requiresAuth: true } },
		{ path: '/alumni', name: 'alumni', component: AlumniView, meta: { requiresAuth: true } },
		{ path: '/messages', name: 'messages', component: MessagesView, meta: { requiresAuth: true } },
		{ path: '/resumes', name: 'resumes', component: ResumesView, meta: { requiresAuth: true } },
		{ path: '/events', name: 'events', component: EventsView, meta: { requiresAuth: true } },
		{ path: '/mentorship', name: 'mentorship', component: MentorshipView, meta: { requiresAuth: true } },
		{ path: '/communities', name: 'communities', component: CommunitiesView, meta: { requiresAuth: true } },
		
		// Admin only routes
		{ path: '/alumni-management', name: 'alumni-management', component: AlumniManagementView, meta: { requiresAuth: true, requiresRole: 'admin' } },
		{ path: '/analytics', name: 'analytics', component: AnalyticsView, meta: { requiresAuth: true } },
	],
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
	const authStore = useAuthStore()
	
	// Check if route requires authentication
	if (to.meta.requiresAuth) {
		// Check if user is authenticated
		if (!authStore.isAuthenticated) {
			// Try to initialize auth from localStorage
			const isAuth = await authStore.initAuth()
			if (!isAuth) {
				return next('/login')
			}
		}
		
		// Check role-based access
		if (to.meta.requiresRole && authStore.userRole !== to.meta.requiresRole) {
			// Redirect to dashboard if user doesn't have required role
			return next('/')
		}
	}
	
	// Check if route requires guest (not authenticated)
	if (to.meta.requiresGuest && authStore.isAuthenticated) {
		return next('/')
	}
	
	next()
})

export default router


