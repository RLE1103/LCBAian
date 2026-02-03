<template>
  <aside 
    id="mobile-sidebar"
    role="navigation"
    aria-label="Main navigation"
    :aria-hidden="!isMobileMenuOpen && 'true'"
    :class="[
      'bg-blue-900 text-white flex flex-col shadow-lg overflow-hidden transition-all duration-300 ease-in-out',
      'fixed inset-y-0 left-0 md:relative md:inset-auto md:h-full',
      'z-50',
      // Width classes
      isCollapsed ? 'w-0 md:w-16' : 'w-64',
      // Transform classes for mobile
      isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
    ]"
  >
    <div class="flex flex-col h-full overflow-hidden">
      <!-- LCBA Logo Placeholder -->
      <div class="flex items-center gap-3 p-4 border-b border-blue-800 flex-shrink-0" :class="{ 'justify-center': isCollapsed }">
        <img src="\src\assets\images\LCBAlogo.png" alt="LCBA Logo" :class="isCollapsed ? 'w-10 h-10' : 'w-16 h-16'" class="rounded object-cover" />
        <div v-if="!isCollapsed" class="font-bold text-lg leading-tight">LCBAConnect+</div>
      </div>
      
      <!-- Logo and User -->
      <div class="flex items-center gap-3 p-4 border-b border-blue-800 flex-shrink-0" :class="{ 'justify-center': isCollapsed }">
        <div class="w-12 h-12 rounded-full border-4 border-yellow-400 flex items-center justify-center bg-blue-700 overflow-hidden">
          <img :src="userAvatar" :alt="userFullName" class="w-10 h-10 rounded-full" />
        </div>
        <div v-if="!isCollapsed">
          <div class="text-sm font-semibold leading-tight">{{ userFullName }}</div>
          <div class="text-xs leading-tight text-blue-300">{{ userRole }}</div>
        </div>
      </div>
      
      <!-- Navigation (Scrollable) -->
      <nav class="flex-1 overflow-y-auto overflow-x-hidden mt-6 min-h-0" aria-label="Primary navigation">
        <ul class="space-y-2 pb-4" role="list">
          <li v-if="!isCollapsed" class="text-xs text-blue-300 px-4" role="presentation" aria-hidden="true">Features</li>
          <li>
            <RouterLink to="/" :class="linkClasses('/')" @click="handleNavClick" :title="isCollapsed ? 'Dashboard' : ''" aria-label="Dashboard">
              <span aria-hidden="true">🏠</span><span v-if="!isCollapsed">Dashboard</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/messages" :class="linkClasses('/messages', 'relative')" @click="handleNavClick" :title="isCollapsed ? 'Messages' : ''" aria-label="Messages">
              <span aria-hidden="true">✉️</span><span v-if="!isCollapsed">Messages</span>
              <span v-if="unreadCount > 0 && !isCollapsed" class="ml-auto text-xs bg-red-500 text-white rounded-full px-2 absolute right-4" aria-label="`${unreadCount} unread messages`">{{ unreadCount }}</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/profile" :class="linkClasses('/profile')" @click="handleNavClick" :title="isCollapsed ? 'Profile' : ''" aria-label="Profile">
              <span aria-hidden="true">👤</span><span v-if="!isCollapsed">Profile</span>
            </RouterLink>
          </li>
          <li v-if="!isCollapsed" class="mt-4 text-xs text-blue-300 px-4">Recruitment</li>
          <li>
            <RouterLink to="/jobs" :class="linkClasses('/jobs')" @click="handleNavClick" :title="isCollapsed ? 'Jobs' : ''">
              <span>💼</span><span v-if="!isCollapsed">Jobs</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/alumni" :class="linkClasses('/alumni')" @click="handleNavClick" :title="isCollapsed ? 'Alumni' : ''">
              <span>👥</span><span v-if="!isCollapsed">Alumni</span>
            </RouterLink>
          </li>
          <li v-if="!isCollapsed" class="mt-4 text-xs text-blue-300 px-4">Organization</li>
          <li v-if="isAdmin">
            <RouterLink to="/alumni-management" :class="linkClasses('/alumni-management')" @click="handleNavClick" :title="isCollapsed ? 'Alumni Management' : ''">
              <span>🏢</span><span v-if="!isCollapsed">Alumni Management</span>
            </RouterLink>
          </li>
          <li v-if="isAdmin">
            <RouterLink to="/intelligent-tracker" :class="linkClasses('/intelligent-tracker')" @click="handleNavClick" :title="isCollapsed ? 'Alumni Tracker' : ''">
              <span>📊</span><span v-if="!isCollapsed">Alumni Tracker</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/events" :class="linkClasses('/events')" @click="handleNavClick" :title="isCollapsed ? 'Events' : ''">
              <span>📅</span><span v-if="!isCollapsed">Events</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/news" :class="linkClasses('/news')" @click="handleNavClick" :title="isCollapsed ? 'News and Announcements' : ''">
              <span>📰</span><span v-if="!isCollapsed">News and Announcements</span>
            </RouterLink>
          </li>
        </ul>
      </nav>
      
      <!-- Quick Links Section (Fixed at bottom) -->
      <div v-if="!isCollapsed" class="border-t border-blue-800 p-4 flex-shrink-0 bg-blue-900">
        <div class="text-xs text-blue-300 mb-2">Quick Links</div>
        <div class="space-y-1 text-xs">
          <a href="https://lcba.edu.ph" target="_blank" class="flex items-center gap-2 text-blue-200 hover:text-white py-1">
            <span>🌐</span>
            <span>LCBA Website</span>
          </a>
          <router-link to="/terms" class="flex items-center gap-2 text-blue-200 hover:text-white py-1">
            <span>📋</span>
            <span>Community Guidelines</span>
          </router-link>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { computed, ref, onMounted } from 'vue'
import axios from '../config/api'

// Props
const props = defineProps({
  isMobileMenuOpen: {
    type: Boolean,
    default: false
  },
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['close'])

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
	const base = 'flex items-center px-4 py-2 rounded-r-full transition-all '
	const gap = props.isCollapsed ? 'justify-center ' : 'gap-3 '
	const hover = isActive ? '' : 'hover:bg-blue-800 '
	const active = isActive ? 'bg-yellow-400 text-blue-900 font-bold ' : ''
	return base + gap + hover + active + (extra ? extra + ' ' : '')
}

// Close mobile menu when clicking a link
const handleNavClick = () => {
  if (props.isMobileMenuOpen) {
    emit('close')
  }
}
</script>
