<template>
  <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
    <div class="flex items-center justify-between mb-3 md:mb-4">
      <h2 class="text-base md:text-lg font-semibold text-gray-900">
        <span class="mr-2">🎂</span>Birthday Corner
      </h2>
      <button @click="refreshBirthdays" class="text-blue-600 hover:text-blue-800 text-sm touch-manipulation">
        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
      </button>
    </div>

    <!-- Tab Navigation -->
    <div class="flex space-x-1 md:space-x-2 mb-3 md:mb-4 border-b border-gray-200 overflow-x-auto">
      <button
        @click="activeTab = 'today'"
        :class="[
          'px-2 md:px-3 py-2 text-xs md:text-sm font-medium border-b-2 transition-colors whitespace-nowrap touch-manipulation',
          activeTab === 'today' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'
        ]"
      >
        Today
      </button>
      <button
        @click="activeTab = 'week'"
        :class="[
          'px-3 py-2 text-sm font-medium border-b-2 transition-colors',
          activeTab === 'week' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'
        ]"
      >
        This Week
      </button>
      <button
        @click="activeTab = 'upcoming'"
        :class="[
          'px-3 py-2 text-sm font-medium border-b-2 transition-colors',
          activeTab === 'upcoming' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'
        ]"
      >
        Upcoming
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <p class="text-gray-500 text-sm mt-2">Loading birthdays...</p>
    </div>

    <!-- Birthday List -->
    <div v-else-if="currentBirthdays.length > 0" class="space-y-3 max-h-96 overflow-y-auto">
      <div
        v-for="person in currentBirthdays"
        :key="person.id"
        class="flex items-center space-x-3 p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg hover:shadow-md transition-shadow"
      >
        <img
          :src="getAvatar(person)"
          :alt="getFullName(person)"
          class="w-12 h-12 rounded-full border-2 border-yellow-400"
        />
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">
            {{ getFullName(person) }}
          </p>
          <p class="text-xs text-gray-500 truncate">{{ person.headline || 'LCBA Alumni' }}</p>
          <p class="text-xs text-blue-600 mt-1">
            🎉 {{ formatBirthday(person.birthdate) }}
          </p>
        </div>
        <button
          @click="sendWishes(person)"
          class="px-3 py-1 text-xs bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors"
        >
          Send Wishes
        </button>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-8">
      <span class="text-4xl">🎈</span>
      <p class="text-gray-500 text-sm mt-2">No birthdays {{ getEmptyMessage() }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from '../config/api'

const router = useRouter()
const authStore = useAuthStore()

const activeTab = ref('today')
const loading = ref(false)
const birthdaysToday = ref([])
const birthdaysThisWeek = ref([])
const birthdaysUpcoming = ref([])

const currentBirthdays = computed(() => {
  switch (activeTab.value) {
    case 'today':
      return birthdaysToday.value
    case 'week':
      return birthdaysThisWeek.value
    case 'upcoming':
      return birthdaysUpcoming.value
    default:
      return []
  }
})

const fetchBirthdays = async () => {
  if (!authStore.isAuthenticated) {
    birthdaysToday.value = []
    birthdaysThisWeek.value = []
    birthdaysUpcoming.value = []
    return
  }
  loading.value = true
  try {
    const [todayRes, weekRes, upcomingRes] = await Promise.all([
      axios.get('/api/birthdays/today'),
      axios.get('/api/birthdays/this-week'),
      axios.get('/api/birthdays/upcoming')
    ])

    if (todayRes?.data?.success) {
      birthdaysToday.value = todayRes.data.data
    }
    if (weekRes?.data?.success) {
      birthdaysThisWeek.value = weekRes.data.data
    }
    if (upcomingRes?.data?.success) {
      birthdaysUpcoming.value = upcomingRes.data.data
    }
  } catch (error) {
    if (error.response?.status === 401) {
      birthdaysToday.value = []
      birthdaysThisWeek.value = []
      birthdaysUpcoming.value = []
      return
    }
    console.error('Error fetching birthdays:', error)
  } finally {
    loading.value = false
  }
}

const refreshBirthdays = () => {
  if (!authStore.isAuthenticated) {
    return
  }
  fetchBirthdays()
}

const getFullName = (person) => {
  let name = person.first_name
  if (person.middle_name) name += ' ' + person.middle_name
  name += ' ' + person.last_name
  if (person.suffix) name += ' ' + person.suffix
  return name
}

const getAvatar = (person) => {
  if (person.profile_picture) {
    const picture = person.profile_picture
    if (picture.startsWith('http://') || picture.startsWith('https://')) return picture
    const baseUrl = axios.defaults.baseURL || 'http://localhost:8000'
    if (picture.startsWith('/uploads/')) return baseUrl + picture
    if (picture.startsWith('uploads/')) return `${baseUrl}/${picture}`
    return `${baseUrl}/uploads/profile_pictures/${picture}`
  }
  const name = getFullName(person)
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=128`
}

const formatBirthday = (birthdate) => {
  const date = new Date(birthdate)
  const today = new Date()
  const month = date.toLocaleString('default', { month: 'short' })
  const day = date.getDate()
  
  // Check if it's today
  if (date.getDate() === today.getDate() && date.getMonth() === today.getMonth()) {
    return 'Today!'
  }
  
  return `${month} ${day}`
}

const getEmptyMessage = () => {
  switch (activeTab.value) {
    case 'today':
      return 'today'
    case 'week':
      return 'this week'
    case 'upcoming':
      return 'in the next 30 days'
    default:
      return ''
  }
}

const sendWishes = (person) => {
  // Navigate to messages with the person
  router.push({ path: '/messages', query: { user: person.id } })
}

onMounted(async () => {
  const isAuthenticated = await authStore.checkAuth()
  if (!isAuthenticated) {
    return
  }
  fetchBirthdays()
})
</script>
