<template>
  <aside 
    id="mobile-sidebar"
    role="navigation"
    aria-label="Main navigation"
    :aria-hidden="isSidebarHidden ? 'true' : null"
    :inert="isSidebarHidden ? '' : null"
    ref="sidebarRef"
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
      <div class="md:hidden flex items-center justify-start px-4 pt-4">
        <button
          v-if="isMobileMenuOpen"
          @click="emit('close')"
          class="bg-red-600 text-white p-2 rounded-lg shadow-lg"
          aria-label="Close menu"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <!-- Navigation (Scrollable) -->
      <nav class="flex-1 overflow-y-auto overflow-x-hidden mt-6 min-h-0" aria-label="Primary navigation">
        <ul class="space-y-2 pb-4" role="list">
          <li v-if="!isCollapsed" class="text-xs text-blue-300 px-4" role="presentation" aria-hidden="true">Features</li>
          <li>
            <RouterLink to="/" :class="linkClasses('/')" @click="handleNavClick" :title="isCollapsed ? 'Dashboard' : ''" aria-label="Dashboard">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h4a1 1 0 001-1V14a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 001 1h4a1 1 0 001-1V10"></path>
              </svg>
              <span v-if="!isCollapsed">Dashboard</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/messages" :class="linkClasses('/messages', 'relative')" @click="handleNavClick" :title="isCollapsed ? 'Messages' : ''" aria-label="Messages">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 6 9-6"></path>
              </svg>
              <span v-if="!isCollapsed">Messages</span>
              <span v-if="unreadCount > 0 && !isCollapsed" class="ml-auto text-xs bg-red-500 text-white rounded-full px-2 absolute right-4" aria-label="`${unreadCount} unread messages`">{{ unreadCount }}</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/profile" :class="linkClasses('/profile')" @click="handleNavClick" :title="isCollapsed ? 'Profile' : ''" aria-label="Profile">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A6.002 6.002 0 0112 14c2.21 0 4.21 1.195 5.121 3.804M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 100-18 9 9 0 000 18z"></path>
              </svg>
              <span v-if="!isCollapsed">Profile</span>
            </RouterLink>
          </li>
          <li v-if="!isCollapsed" class="mt-4 text-xs text-blue-300 px-4">Recruitment</li>
          <li>
            <RouterLink to="/jobs" :class="linkClasses('/jobs')" @click="handleNavClick" :title="isCollapsed ? 'Jobs' : ''">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13v4a2 2 0 002 2h14a2 2 0 002-2v-4"></path>
              </svg>
              <span v-if="!isCollapsed">Jobs</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/alumni" :class="linkClasses('/alumni')" @click="handleNavClick" :title="isCollapsed ? 'Alumni' : ''">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" viewBox="0 0 11 13" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7.84241 8.14087L5.5 10.5631L3.15759 8.14087C1.40201 8.21958 0 9.70493 0 11.5381V11.7819C0 12.4547 0.527902 13.0006 1.17857 13.0006H9.82143C10.4721 13.0006 11 12.4547 11 11.7819V11.5381C11 9.70493 9.59799 8.21958 7.84241 8.14087ZM0.333929 2.02681L0.491071 2.06489V3.5477C0.319196 3.65435 0.196429 3.8397 0.196429 4.06313C0.196429 4.27642 0.309375 4.45415 0.468973 4.56333L0.0859375 6.14517C0.0441964 6.32036 0.1375 6.50063 0.272545 6.50063H1.29888C1.43393 6.50063 1.52723 6.32036 1.48549 6.14517L1.10246 4.56333C1.26205 4.45415 1.375 4.27642 1.375 4.06313C1.375 3.8397 1.25223 3.65435 1.08036 3.5477V2.21216L2.70089 2.61587C2.48973 3.05259 2.35714 3.54009 2.35714 4.06313C2.35714 5.85825 3.76406 7.31313 5.5 7.31313C7.23594 7.31313 8.64286 5.85825 8.64286 4.06313C8.64286 3.54009 8.51272 3.05259 8.29911 2.61587L10.6636 2.02681C11.1105 1.91509 11.1105 1.33872 10.6636 1.227L5.98862 0.0590332C5.66942 -0.0196777 5.33304 -0.0196777 5.01384 0.0590332L0.333929 1.22446C-0.110491 1.33618 -0.110491 1.91509 0.333929 2.02681Z"/>
              </svg>
              <span v-if="!isCollapsed">Alumni</span>
            </RouterLink>
          </li>
          <li v-if="!isCollapsed" class="mt-4 text-xs text-blue-300 px-4">Organization</li>
          <li v-if="isAdmin">
            <RouterLink to="/alumni-management" :class="linkClasses('/alumni-management')" @click="handleNavClick" :title="isCollapsed ? 'Alumni Management' : ''">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 21V7a2 2 0 012-2h4v16"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 21V3h4a2 2 0 012 2v16"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h2M9 13h2M9 17h2M15 7h2M15 11h2M15 15h2"></path>
              </svg>
              <span v-if="!isCollapsed">Alumni Management</span>
            </RouterLink>
          </li>
          <li v-if="isAdmin">
            <RouterLink to="/intelligent-tracker" :class="linkClasses('/intelligent-tracker')" @click="handleNavClick" :title="isCollapsed ? 'Alumni Tracker' : ''">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V9"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V5"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16v-7"></path>
              </svg>
              <span v-if="!isCollapsed">Alumni Tracker</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/events" :class="linkClasses('/events')" @click="handleNavClick" :title="isCollapsed ? 'Events' : ''">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span v-if="!isCollapsed">Events</span>
            </RouterLink>
          </li>
          <li>
            <RouterLink to="/news" :class="linkClasses('/news')" @click="handleNavClick" :title="isCollapsed ? 'News and Announcements' : ''">
              <svg aria-hidden="true" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 5v12a2 2 0 002 2h0a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6"></path>
              </svg>
              <span v-if="!isCollapsed">News and Announcements</span>
            </RouterLink>
          </li>
        </ul>
      </nav>
      
      <!-- Quick Links Section (Fixed at bottom) -->
      <div v-if="!isCollapsed" class="border-t border-blue-800 p-4 flex-shrink-0 bg-blue-900">
        <div class="text-xs text-blue-300 mb-2">Quick Links</div>
        <div class="space-y-1 text-xs">
          <a href="https://lcba.edu.ph" target="_blank" class="flex items-center gap-2 text-blue-200 hover:text-white py-1">
            <svg aria-hidden="true" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a9 9 0 100 18 9 9 0 000-18z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12h18M12 3c2.5 2.6 4 5.7 4 9s-1.5 6.4-4 9c-2.5-2.6-4-5.7-4-9s1.5-6.4 4-9z"></path>
            </svg>
            <span>LCBA Website</span>
          </a>
          <router-link to="/terms" class="flex items-center gap-2 text-blue-200 hover:text-white py-1">
            <svg aria-hidden="true" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6a2 2 0 012 2v12a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6M9 17h6"></path>
            </svg>
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
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

const sidebarRef = ref(null)
const isSmallScreen = ref(false)
const isSidebarHidden = computed(() => isSmallScreen.value && !props.isMobileMenuOpen)

const mediaQuery = window.matchMedia('(min-width: 768px)')
const updateScreenSize = () => {
  isSmallScreen.value = !mediaQuery.matches
}

onMounted(() => {
  updateScreenSize()
  mediaQuery.addEventListener('change', updateScreenSize)
})

onUnmounted(() => {
  mediaQuery.removeEventListener('change', updateScreenSize)
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

watch(isSidebarHidden, (hidden) => {
  if (hidden && sidebarRef.value) {
    const active = document.activeElement
    if (active && sidebarRef.value.contains(active)) {
      active.blur()
    }
  }
})
</script>
