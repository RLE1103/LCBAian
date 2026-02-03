<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import Sidebar from './components/Sidebar.vue'
import Topbar from './components/Topbar.vue'
import ToastNotification from './components/ToastNotification.vue'
import { useToast } from './composables/useToast'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isInitialized = ref(false)
const isMobileMenuOpen = ref(false)
const isSidebarCollapsed = ref(false)

// Global error handler for Vue
import { getCurrentInstance } from 'vue'
const app = getCurrentInstance()

if (app) {
  app.appContext.config.errorHandler = (err, instance, info) => {
    console.error('Vue Error:', err)
    console.error('Error Info:', info)
    toast.error('An unexpected error occurred. Please try again.', 'Error')
  }

  // Handle unhandled promise rejections
  window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled Promise Rejection:', event.reason)
    toast.error('An unexpected error occurred. Please try again.', 'Error')
    event.preventDefault()
  })
}

onMounted(async () => {
  // Initialize authentication
  await authStore.initAuth()
  isInitialized.value = true
  
  // Redirect to login if not authenticated and trying to access protected routes
  if (!authStore.isAuthenticated && route.meta.requiresAuth) {
    router.push('/login')
  }
})

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

const toggleSidebar = () => {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
}

// Toast notifications
const { toasts, remove } = useToast()
</script>

<template>
  <!-- Skip to main content link for accessibility -->
  <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[9999] focus:bg-blue-600 focus:text-white focus:px-4 focus:py-2 focus:rounded">
    Skip to main content
  </a>

  <div v-if="!isInitialized" class="flex h-screen bg-blue-700 items-center justify-center" role="status" aria-live="polite">
    <div class="text-center text-white">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4" aria-hidden="true"></div>
      <p>Loading...</p>
    </div>
  </div>
  
  <div v-else-if="authStore.isAuthenticated && !route.meta.hideNavigation" class="flex flex-col h-screen">
    <!-- Topbar -->
    <header role="banner">
      <Topbar />
    </header>
    
    <!-- Main Layout: Sidebar + Content -->
    <div class="flex flex-1 relative overflow-hidden">
      <!-- Mobile Menu Overlay (Transparent) -->
      <div 
        v-if="isMobileMenuOpen"
        @click="closeMobileMenu"
        class="md:hidden fixed inset-0 bg-transparent z-40"
      ></div>

      <!-- Mobile Menu Button (visible on small screens) -->
      <button 
        @click="toggleMobileMenu"
        class="md:hidden fixed top-4 left-4 z-[60] bg-blue-900 text-white p-3 rounded-lg shadow-lg transition-colors"
        :class="{ 'bg-red-600': isMobileMenuOpen }"
        :aria-label="isMobileMenuOpen ? 'Close menu' : 'Open menu'"
        :aria-expanded="isMobileMenuOpen"
        aria-controls="mobile-sidebar"
      >
        <svg v-if="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>

      <!-- Desktop Sidebar Toggle Button (visible on medium+ screens) -->
      <button 
        @click="toggleSidebar"
        class="hidden md:block fixed top-20 left-4 z-[60] bg-blue-900 text-white p-2 rounded-lg shadow-lg transition-all hover:bg-blue-800"
        :class="{ 'left-4': isSidebarCollapsed, 'left-[260px]': !isSidebarCollapsed }"
        :aria-label="isSidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        :aria-expanded="!isSidebarCollapsed"
        aria-controls="desktop-sidebar"
      >
        <svg v-if="!isSidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
        </svg>
      </button>

      <!-- Sidebar (Desktop + Mobile Slide-in) -->
      <Sidebar 
        :isMobileMenuOpen="isMobileMenuOpen" 
        :isCollapsed="isSidebarCollapsed"
        @close="closeMobileMenu" 
      />
      
      <!-- Main Content -->
      <main id="main-content" role="main" class="flex-1 bg-blue-100 w-full min-w-0 relative z-10 flex flex-col overflow-hidden" aria-label="Main content">
        <div class="flex-1 overflow-y-auto overflow-x-hidden">
          <router-view />
        </div>
      </main>
    </div>
  </div>
  
  <div v-else class="h-screen bg-blue-700">
    <router-view />
  </div>

  <!-- Toast Notifications Container -->
  <ToastNotification
    v-for="toast in toasts"
    :key="toast.id"
    v-bind="toast"
    @close="remove"
  />
</template>

<style>
body {
  margin: 0;
}

/* Accessibility - Screen reader only class */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}

.sr-only:focus,
.sr-only:active {
  position: static;
  width: auto;
  height: auto;
  overflow: visible;
  clip: auto;
  white-space: normal;
}

.focus\:not-sr-only:focus {
  position: static !important;
  width: auto !important;
  height: auto !important;
  overflow: visible !important;
  clip: auto !important;
  white-space: normal !important;
}
</style>
