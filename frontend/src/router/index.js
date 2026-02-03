import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import DashboardView from '../views/DashboardView.vue'
import JobsView from '../views/JobsView.vue'
import AlumniView from '../views/AlumniView.vue'
import MessagesView from '../views/MessagesView.vue'
// removed ResumesView
import AlumniManagementView from '../views/AlumniManagementView.vue'
import EventsView from '../views/EventsView.vue'
// removed MentorshipView
// removed CommunitiesView
import AnalyticsView from '../views/AnalyticsView.vue'
import ProfileView from '../views/ProfileView.vue'
import NewsAnnouncementsView from '../views/NewsAnnouncementsView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import TermsGuidelinesView from '../views/TermsGuidelinesView.vue'
import PendingVerificationView from '../views/PendingVerificationView.vue'

const router = createRouter({
	history: createWebHistory(),
	routes: [
		// Public routes
		{ path: '/login', name: 'login', component: LoginView, meta: { requiresGuest: true } },
		{ path: '/terms-guidelines', name: 'terms-guidelines', component: TermsGuidelinesView, meta: { hideNavigation: true } },
		{ path: '/register', name: 'register', component: RegisterView, meta: { requiresGuest: true, hideNavigation: true } },
		{ path: '/terms', redirect: '/terms-guidelines' },
		{ path: '/pending-verification', name: 'pending-verification', component: PendingVerificationView, meta: { requiresAuth: true, hideNavigation: true, allowUnverified: true } },
		
		// Protected routes
		{ path: '/', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
		{ path: '/jobs', name: 'jobs', component: JobsView, meta: { requiresAuth: true } },
		{ path: '/alumni', name: 'alumni', component: AlumniView, meta: { requiresAuth: true } },
		{ path: '/messages', name: 'messages', component: MessagesView, meta: { requiresAuth: true } },
		// resumes route removed
		{ path: '/events', name: 'events', component: EventsView, meta: { requiresAuth: true } },
		// mentorship route removed
		// communities route removed
		{ path: '/profile', name: 'profile', component: ProfileView, meta: { requiresAuth: true } },
		{ path: '/news', name: 'news', component: NewsAnnouncementsView, meta: { requiresAuth: true } },
		
		// Admin only routes
		{ path: '/alumni-management', name: 'alumni-management', component: AlumniManagementView, meta: { requiresAuth: true, requiresRole: 'admin' } },
		{ path: '/intelligent-tracker', name: 'intelligent-tracker', component: AnalyticsView, meta: { requiresAuth: true, requiresRole: 'admin' } },
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
		
		// Check verification status (except for allowed routes and admins)
		if (!to.meta.allowUnverified && authStore.user && !authStore.user.is_verified && authStore.userRole !== 'admin') {
			// Redirect unverified users to pending verification page
			if (to.path !== '/pending-verification') {
				return next('/pending-verification')
			}
		}
		
		// If user is verified and trying to access pending verification, redirect to dashboard
		if (to.path === '/pending-verification' && authStore.user?.is_verified) {
			return next('/')
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


