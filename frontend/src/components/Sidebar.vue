<template>
  <aside class="w-64 bg-blue-900 text-white flex flex-col justify-between h-screen shadow-lg">
    <div>
      <!-- LCBA Logo Placeholder -->
      <div class="flex items-center gap-3 p-4 border-b border-blue-800">
        <img src="\src\assets\images\LCBAlogo.png" alt="LCBA Logo" class="w-16 h-16 rounded object-cover" />
        <div class="font-bold text-lg leading-tight">LCBAian</div>
      </div>
      <!-- Logo and User -->
      <div class="flex items-center gap-3 p-4 border-b border-blue-800">
        <div class="w-12 h-12 rounded-full border-4 border-yellow-400 flex items-center justify-center bg-blue-700 overflow-hidden">
          <img :src="userAvatar" :alt="userFullName" class="w-10 h-10 rounded-full" />
        </div>
        <div>
          <div class="text-sm font-semibold leading-tight">{{ userFullName }}</div>
          <div class="text-xs leading-tight text-blue-300">{{ userRole }}</div>
        </div>
      </div>
      <!-- Navigation -->
      <nav class="mt-6 flex-1">
        <ul class="space-y-2">
          <li class="mt-4 text-xs text-blue-300 px-4">Features</li>
          <li><RouterLink to="/" :class="linkClasses('/')"><span>🏠</span>Dashboard</RouterLink></li>
          <li><RouterLink to="/messages" :class="linkClasses('/messages', 'relative')"><span>✉️</span>Messages <span v-if="unreadCount > 0" class="ml-auto text-xs bg-red-500 text-white rounded-full px-2 absolute right-4">{{ unreadCount }}</span></RouterLink></li>
          <li class="mt-4 text-xs text-blue-300 px-4">Recruitment</li>
          <li><RouterLink to="/jobs" :class="linkClasses('/jobs')"><span>💼</span>Jobs</RouterLink></li>
          <li><RouterLink to="/alumni" :class="linkClasses('/alumni')"><span>👥</span>Alumni</RouterLink></li>
          <li><RouterLink to="/resumes" :class="linkClasses('/resumes')"><span>📄</span>Resumes</RouterLink></li>
          <li class="mt-4 text-xs text-blue-300 px-4">Organization</li>
          <li v-if="isAdmin"><RouterLink to="/alumni-management" :class="linkClasses('/alumni-management')"><span>🏢</span>Alumni Management</RouterLink></li>
          <li><RouterLink to="/analytics" :class="linkClasses('/analytics')"><span>📊</span>AI Analytics</RouterLink></li>
          <li><RouterLink to="/events" :class="linkClasses('/events')"><span>📅</span>Events</RouterLink></li>
          <li><RouterLink to="/mentorship" :class="linkClasses('/mentorship')"><span>🧭</span>Mentorship</RouterLink></li>
          <li><RouterLink to="/communities" :class="linkClasses('/communities')"><span>🌐</span>Communities</RouterLink></li>
        </ul>
      </nav>
    </div>
    <!-- Bottom Pattern -->
    <div class="relative">
      <button @click="handleLogout" class="m-4 w-[85%] bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg font-bold shadow">Log Out</button>
    </div>
  </aside>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { computed, ref, onMounted } from 'vue'
import axios from '../config/api'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// Computed properties for user data
const userFullName = computed(() => authStore.fullName || 'User')
const userRole = computed(() => authStore.userRole || 'User')
const isAdmin = computed(() => authStore.isAdmin)

// Unread messages count
const unreadCount = ref(0)

// Fetch unread messages count
const fetchUnreadCount = async () => {
  try {
    const response = await axios.get('/api/messages')
    if (response?.data?.success && Array.isArray(response.data.data)) {
      unreadCount.value = response.data.data.reduce((sum, conv) => sum + (conv.unread_count || 0), 0)
    }
  } catch (error) {
    console.error('Error fetching unread count:', error)
  }
}

onMounted(() => {
  fetchUnreadCount()
})

const userAvatar = computed(() => {
  const name = userFullName.value
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1e3a8a&color=fff&size=128`
})

function linkClasses(targetPath, extra = '') {
	const isActive = route.path === targetPath
	const base = 'flex items-center gap-3 px-4 py-2 rounded-r-full '
	const hover = isActive ? '' : 'hover:bg-blue-800 '
	const active = isActive ? 'bg-yellow-400 text-blue-900 font-bold ' : ''
	return base + hover + active + (extra ? extra + ' ' : '')
}

const handleLogout = () => {
  authStore.logout()
  router.push('/login')
}
</script>
