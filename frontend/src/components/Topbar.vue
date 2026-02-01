<template>
  <header class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg sticky top-0 z-50">
    <div class="px-3 md:px-6 py-2 md:py-3 flex justify-between items-center">
      <!-- Left: Logo and Site Name -->
      <div class="flex items-center gap-2 md:gap-3">
        <img src="/src/assets/images/LCBAlogo.png" alt="LCBA Logo" class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-white shadow-md" />
        <div>
          <span class="text-base md:text-xl font-bold tracking-wide">LCBAConnect+</span>
          <p class="text-xs text-blue-200 hidden sm:block">Alumni Network Platform</p>
        </div>
      </div>

      <!-- Right: User Profile Dropdown -->
      <div class="relative" ref="dropdownRef">
        <button 
          @click="toggleDropdown"
          class="flex items-center gap-2 md:gap-3 hover:bg-blue-700 px-2 md:px-3 py-2 rounded-lg transition-colors touch-manipulation"
        >
          <!-- User Avatar/Initials -->
          <div class="w-9 h-9 md:w-10 md:h-10 rounded-full bg-blue-600 border-2 border-white flex items-center justify-center font-bold text-xs md:text-sm shadow-md">
            {{ getUserInitials() }}
          </div>
          
          <!-- User Name and Role -->
          <div class="hidden lg:block text-left">
            <p class="text-sm font-medium">{{ authStore.user?.first_name }} {{ authStore.user?.last_name }}</p>
            <p class="text-xs text-blue-200">{{ formatRole(authStore.user?.role) }}</p>
          </div>
          
          <!-- Dropdown Icon -->
          <svg 
            class="w-4 h-4 transition-transform"
            :class="{ 'rotate-180': showDropdown }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div 
            v-if="showDropdown"
            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-2xl py-2 text-gray-800 border border-gray-200 z-[9999]"
          >
            <!-- User Info Section -->
            <div class="px-4 py-3 border-b border-gray-200">
              <p class="text-sm font-semibold text-gray-900">{{ authStore.user?.first_name }} {{ authStore.user?.last_name }}</p>
              <p class="text-xs text-gray-600">{{ authStore.user?.email }}</p>
              <span class="inline-block mt-2 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                {{ formatRole(authStore.user?.role) }}
              </span>
            </div>

            <!-- Menu Items -->
            <router-link
              to="/profile"
              @click="closeDropdown"
              class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition-colors"
            >
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <span class="text-sm">My Profile</span>
            </router-link>

            <router-link
              to="/"
              @click="closeDropdown"
              class="flex items-center gap-3 px-4 py-2 hover:bg-gray-100 transition-colors"
            >
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              <span class="text-sm">Dashboard</span>
            </router-link>

            <div class="border-t border-gray-200 my-2"></div>

            <button
              @click="handleLogout"
              class="flex items-center gap-3 px-4 py-2 hover:bg-red-50 transition-colors w-full text-left"
            >
              <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              <span class="text-sm text-red-600 font-medium">Logout</span>
            </button>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const showDropdown = ref(false)
const dropdownRef = ref(null)

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const closeDropdown = () => {
  showDropdown.value = false
}

const getUserInitials = () => {
  const firstName = authStore.user?.first_name || ''
  const lastName = authStore.user?.last_name || ''
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'U'
}

const formatRole = (role) => {
  if (!role) return ''
  return role.charAt(0).toUpperCase() + role.slice(1)
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
