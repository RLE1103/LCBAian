<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from './stores/auth'
import Sidebar from './components/Sidebar.vue'
import Topbar from './components/Topbar.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const isInitialized = ref(false)
const isMobileMenuOpen = ref(false)
const isSidebarCollapsed = ref(false)

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
</script>

<template>
  <div v-if="!isInitialized" class="flex h-screen bg-blue-700 items-center justify-center">
    <div class="text-center text-white">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
      <p>Loading...</p>
    </div>
  </div>
  
  <div v-else-if="authStore.isAuthenticated && !route.meta.hideNavigation" class="flex flex-col h-screen">
    <!-- Topbar -->
    <Topbar />
    
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
      <main class="flex-1 bg-blue-100 w-full min-w-0 relative z-10 flex flex-col overflow-hidden">
        <div class="flex-1 overflow-y-auto overflow-x-hidden">
          <router-view />
        </div>
      </main>
    </div>
  </div>
  
  <div v-else class="h-screen bg-blue-700">
    <router-view />
  </div>
</template>

<style>
body {
  margin: 0;
}
</style>
