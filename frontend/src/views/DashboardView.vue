<template>
  <div class="p-4 md:p-6">
    <!-- Welcome Header -->
    <div class="mb-6 md:mb-8">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Welcome back, {{ userFullName }}!</h1>
          <p class="text-gray-600 mt-1 md:mt-2 text-sm md:text-base">Here's what's happening in your LCBAian network today</p>
        </div>
        <div class="flex items-center space-x-4">
          <button @click="refreshData" :disabled="loading" class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2 text-sm md:text-base disabled:opacity-60 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span class="hidden sm:inline">{{ loading ? 'Refreshing...' : 'Refresh' }}</span>
            </button>
          </div>
          </div>
        </div>
        
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
      <RouterLink to="/messages" class="group">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 md:p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-blue-100 text-xs md:text-sm font-medium">Messages</p>
              <p class="text-2xl md:text-3xl font-bold">{{ stats.messages }}</p>
              <p class="text-blue-200 text-xs">{{ stats.unreadMessages }} unread</p>
            </div>
            <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
            </div>
          </div>
        </div>
      </RouterLink>

      <RouterLink to="/jobs" class="group">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 md:p-6 text-white hover:from-green-600 hover:to-green-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-green-100 text-xs md:text-sm font-medium">Jobs listed in the last 30 days</p>
              <p class="text-2xl md:text-3xl font-bold">{{ stats.jobApplications }}</p>
              <p class="text-green-200 text-xs">Updated daily</p>
            </div>
            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
              </svg>
            </div>
          </div>
        </div>
      </RouterLink>

      <RouterLink to="/alumni" class="group">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 md:p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-purple-100 text-xs md:text-sm font-medium">Connections</p>
              <p class="text-2xl md:text-3xl font-bold">{{ stats.connections }}</p>
              <p class="text-purple-200 text-xs">{{ stats.newConnections }} new this week</p>
            </div>
            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </RouterLink>

      <RouterLink to="/events" class="group">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 md:p-6 text-white hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-orange-100 text-xs md:text-sm font-medium">Events</p>
              <p class="text-2xl md:text-3xl font-bold">{{ stats.events }}</p>
              <p class="text-orange-200 text-xs">{{ stats.upcomingEvents }} upcoming</p>
            </div>
            <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
          </div>
        </div>
      </RouterLink>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
      <!-- Left Column - Recent Activity & Notifications -->
      <div class="lg:col-span-2 space-y-4 md:space-y-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-4 md:p-6">
          <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Recent Activity</h2>
          </div>
          <div class="space-y-3 md:space-y-4">
            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3 md:space-x-4 p-3 md:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <div class="flex-shrink-0">
                <div :class="[
                  'w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center text-white font-bold text-xs md:text-sm',
                  activity.type === 'message' ? 'bg-blue-500' :
                  activity.type === 'job' ? 'bg-green-500' :
                  activity.type === 'connection' ? 'bg-purple-500' :
                  'bg-orange-500'
                ]">
                  {{ activity.icon }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs md:text-sm font-medium text-gray-900">{{ activity.title }}</p>
                <p class="text-xs md:text-sm text-gray-600">{{ activity.description }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ formatTime(activity.timestamp) }}</p>
              </div>
              <div v-if="activity.action" class="flex-shrink-0">
                <button @click="handleActivityAction(activity)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                  {{ activity.action }}
          </button>
              </div>
        </div>
      </div>
    </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h2>
          <div class="grid grid-cols-2 gap-4">
            <RouterLink to="/jobs" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-blue-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-blue-600">Browse Jobs</h3>
                    <p class="text-sm text-gray-600">Find your next opportunity</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <RouterLink to="/alumni" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-purple-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-purple-600">Connect</h3>
                    <p class="text-sm text-gray-600">Expand your network</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <RouterLink to="/events" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-orange-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-orange-600">Events</h3>
                    <p class="text-sm text-gray-600">View upcoming events</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <RouterLink to="/news" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-green-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-green-600">News & Announcements</h3>
                    <p class="text-sm text-gray-600">Stay updated</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <RouterLink to="/profile" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-indigo-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-indigo-600">Profile</h3>
                    <p class="text-sm text-gray-600">Manage your profile</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <!-- Admin-only Quick Actions -->
            <RouterLink v-if="isAdmin" to="/alumni-management" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-red-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-red-600">Alumni Management</h3>
                    <p class="text-sm text-gray-600">Manage alumni</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <RouterLink v-if="isAdmin" to="/intelligent-tracker" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-yellow-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-yellow-600">Alumni Tracker</h3>
                    <p class="text-sm text-gray-600">Analytics & insights</p>
                  </div>
                </div>
              </div>
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- Right Column - Notifications & Updates -->
      <div class="space-y-6">
        <!-- Notifications -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Notifications</h2>
            <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ notifications.length }}</span>
          </div>
          <div class="space-y-3">
            <div v-for="notification in notifications" :key="notification.id" class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <div :class="[
                'w-2 h-2 rounded-full mt-2',
                notification.read ? 'bg-gray-300' : 'bg-blue-500'
              ]"></div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                <p class="text-xs text-gray-600">{{ notification.message }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ formatTime(notification.timestamp) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Birthday Corner -->
        <BirthdayCorner />

        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Upcoming Events</h2>
            <RouterLink to="/events" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</RouterLink>
          </div>
          <div class="space-y-4">
            <div v-for="event in upcomingEvents" :key="event.id" class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
              <div class="flex items-start space-x-3">
                <div class="bg-blue-100 rounded-lg p-2">
                  <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="font-medium text-gray-900 text-sm">{{ event.title }}</h3>
                  <p class="text-xs text-gray-600">{{ event.date }} • {{ event.time }}</p>
                  <p class="text-xs text-gray-500">{{ event.location }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Network Updates -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Network Updates</h2>
          <div class="space-y-4">
            <div v-for="update in networkUpdates" :key="update.id" class="flex items-start space-x-3">
              <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs">
                {{ update.user.first_name[0] }}{{ update.user.last_name[0] }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900">
                  <span class="font-medium">{{ update.user.full_name }}</span> {{ update.action }}
                </p>
                <p class="text-xs text-gray-500">{{ formatTime(update.timestamp) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
      </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import axios from '../config/api'
import BirthdayCorner from '../components/BirthdayCorner.vue'

const authStore = useAuthStore()
const router = useRouter()

// Check if user is admin
const isAdmin = computed(() => authStore.isAdmin)

// User data
const userFullName = computed(() => authStore.fullName || 'User')

// Dashboard statistics
const stats = ref({
  messages: 0,
  unreadMessages: 0,
  jobApplications: 0,
  connections: 0,
  newConnections: 0,
  events: 0,
  upcomingEvents: 0
})

// Recent activity data
const recentActivity = ref([])

// Notifications data
const notifications = ref([])

// Upcoming events
const upcomingEvents = ref([])

// Network updates
const networkUpdates = ref([])

// Loading state
const loading = ref(false)

// Fetch dashboard data
const fetchDashboardData = async () => {
  if (!authStore.isAuthenticated) {
    loading.value = false
    return
  }
  loading.value = true
  try {
    // Fetch messages count
    const messagesResponse = await axios.get('/api/messages')
    const messagesPayload = messagesResponse?.data
    let messagesData = []
    if (Array.isArray(messagesPayload?.data)) {
      messagesData = messagesPayload.data
    } else if (Array.isArray(messagesPayload?.data?.data)) {
      messagesData = messagesPayload.data.data
    } else if (Array.isArray(messagesPayload)) {
      messagesData = messagesPayload
    }
    
    if (messagesResponse.data.success) {
      stats.value.messages = messagesData.length
      stats.value.unreadMessages = messagesData.reduce((sum, conv) => sum + (conv.unread_count || 0), 0)
    }

    // Jobs listed in the last 30 days
    const jobsResponse = await axios.get('/api/job-posts')
    const jobsPayload = jobsResponse?.data
    let jobsData = []
    if (Array.isArray(jobsPayload?.data)) {
      jobsData = jobsPayload.data
    } else if (Array.isArray(jobsPayload?.data?.data)) {
      jobsData = jobsPayload.data.data
    } else if (Array.isArray(jobsPayload)) {
      jobsData = jobsPayload
    }
    
    if (jobsResponse.data.success) {
      const thirtyDaysAgo = new Date(); thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30)
      stats.value.jobApplications = jobsData.filter(j => new Date(j.created_at) >= thirtyDaysAgo).length
    }

    // Fetch events count
    const eventsResponse = await axios.get('/api/events')
    const eventsPayload = eventsResponse?.data
    let eventsData = []
    if (Array.isArray(eventsPayload?.data)) {
      eventsData = eventsPayload.data
    } else if (Array.isArray(eventsPayload?.data?.data)) {
      eventsData = eventsPayload.data.data
    } else if (Array.isArray(eventsPayload)) {
      eventsData = eventsPayload
    }
    
    if (eventsResponse.data.success) {
      stats.value.events = eventsData.length
      stats.value.upcomingEvents = eventsData.filter(event => new Date(event.start_date) > new Date()).length
    }

    // Fetch upcoming events (next 7 days)
    const upcomingResponse = await axios.get('/api/events', {
      params: { upcoming: true }
    })
    const upcomingPayload = upcomingResponse?.data
    let upcomingData = []
    if (Array.isArray(upcomingPayload?.data)) {
      upcomingData = upcomingPayload.data
    } else if (Array.isArray(upcomingPayload?.data?.data)) {
      upcomingData = upcomingPayload.data.data
    } else if (Array.isArray(upcomingPayload)) {
      upcomingData = upcomingPayload
    }
    
    if (upcomingResponse.data.success) {
      upcomingEvents.value = upcomingData.slice(0, 3).map(event => ({
        id: event.id,
        title: event.title,
        date: new Date(event.start_date).toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric'
        }),
        time: new Date(event.start_date).toLocaleTimeString('en-US', {
          hour: 'numeric',
          minute: '2-digit',
          hour12: true
        }),
        location: event.location
      }))
    }

    // Fetch alumni connections count
    const usersResponse = await axios.get('/api/users', { params: { role: 'alumni' } })
    const usersPayload = usersResponse?.data
    let usersData = []
    if (Array.isArray(usersPayload?.data)) {
      usersData = usersPayload.data
    } else if (Array.isArray(usersPayload?.data?.data)) {
      usersData = usersPayload.data.data
    } else if (Array.isArray(usersPayload)) {
      usersData = usersPayload
    }
    
    if (usersResponse.data.success) {
      stats.value.connections = usersData.length
      stats.value.newConnections = Math.floor(usersData.length * 0.1) // 10% as "new connections"
    }

    // Fetch recent activity (messages, job applications, etc.)
    // For now, we'll create a simple activity feed based on available data
    const activity = []
    
    // Add recent messages as activity
    if (messagesData.length > 0) {
      const recentMessage = messagesData[0]
      activity.push({
        id: 1,
        type: 'message',
        icon: '💬',
        title: 'New message received',
        description: `From ${recentMessage.other_user?.first_name} ${recentMessage.other_user?.last_name || 'someone'}`,
        timestamp: new Date(recentMessage.sent_at),
        action: 'Reply'
      })
    }

    // Add recent job applications as activity
    if (jobsData.length > 0) {
      const recentJob = jobsData[0]
      activity.push({
        id: 2,
        type: 'job',
        icon: '💼',
        title: 'New job posted',
        description: `${recentJob.title} at ${recentJob.company_name}`,
        timestamp: new Date(recentJob.created_at),
        action: 'View'
      })
    }

    // Add recent events as activity
    if (eventsData.length > 0) {
      const recentEvent = eventsData[0]
      activity.push({
        id: 3,
        type: 'event',
        icon: '📅',
        title: 'New event posted',
        description: recentEvent.title,
        timestamp: new Date(recentEvent.created_at),
        action: 'View'
      })
    }

    recentActivity.value = activity

    // Fetch network updates (recent posts)
    const postsResponse = await axios.get('/api/posts')
    const postsPayload = postsResponse?.data
    let postsData = []
    if (Array.isArray(postsPayload?.data)) {
      postsData = postsPayload.data
    } else if (Array.isArray(postsPayload?.data?.data)) {
      postsData = postsPayload.data.data
    } else if (Array.isArray(postsPayload)) {
      postsData = postsPayload
    }
    
    if (postsResponse.data.success) {
      networkUpdates.value = postsData.slice(0, 3).map(post => ({
        id: post.post_id,
        user: {
          first_name: post.user?.first_name,
          last_name: post.user?.last_name,
          full_name: `${post.user?.first_name} ${post.user?.last_name}`
        },
        action: 'shared a post',
        timestamp: new Date(post.created_at)
      }))
    }

    const notificationsList = []
    if (stats.value.unreadMessages > 0) {
      const unreadLabel = stats.value.unreadMessages === 1 ? 'message' : 'messages'
      notificationsList.push({
        id: 'unread-messages',
        title: 'Unread messages',
        message: `You have ${stats.value.unreadMessages} unread ${unreadLabel}.`,
        timestamp: messagesData[0]?.sent_at ? new Date(messagesData[0].sent_at) : new Date(),
        read: false
      })
    }

    if (messagesData.length > 0 && stats.value.unreadMessages > 0) {
      const latestMessage = messagesData[0]
      notificationsList.push({
        id: `message-${latestMessage.id || 'latest'}`,
        title: 'New message',
        message: `Message from ${latestMessage.other_user?.first_name || 'someone'}`,
        timestamp: latestMessage.sent_at ? new Date(latestMessage.sent_at) : new Date(),
        read: false
      })
    }

    if (eventsData.length > 0) {
      const latestEvent = eventsData[0]
      notificationsList.push({
        id: `event-new-${latestEvent.id}`,
        title: 'New event posted',
        message: latestEvent.title,
        timestamp: latestEvent.created_at ? new Date(latestEvent.created_at) : new Date(),
        read: false
      })
    }

    if (jobsData.length > 0) {
      const latestJob = jobsData[0]
      notificationsList.push({
        id: `job-${latestJob.id}`,
        title: 'New job posted',
        message: `${latestJob.title} at ${latestJob.company_name}`,
        timestamp: latestJob.created_at ? new Date(latestJob.created_at) : new Date(),
        read: false
      })
    }

    if (postsData.length > 0) {
      const latestPost = postsData[0]
      notificationsList.push({
        id: `post-${latestPost.post_id || latestPost.id || 'latest'}`,
        title: 'New post',
        message: `${latestPost.user?.first_name || 'Someone'} shared a post`,
        timestamp: latestPost.created_at ? new Date(latestPost.created_at) : new Date(),
        read: false
      })
    }

    const cutoffDate = new Date()
    cutoffDate.setDate(cutoffDate.getDate() - 30)

    notifications.value = notificationsList
      .filter(notification => notification.timestamp instanceof Date && !Number.isNaN(notification.timestamp))
      .filter(notification => notification.timestamp >= cutoffDate)
      .sort((a, b) => b.timestamp - a.timestamp)

  } catch (error) {
    if (error.response?.status === 401) {
      return
    }
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

// Methods
const formatTime = (timestamp) => {
  const date = timestamp instanceof Date ? timestamp : new Date(timestamp)
  if (Number.isNaN(date.getTime())) {
    return ''
  }
  const now = new Date()
  const diff = now - date
  if (!Number.isFinite(diff) || diff < 0) {
    return ''
  }
  const minutes = Math.floor(diff / (1000 * 60))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))

  if (minutes < 60) {
    const value = Math.max(1, minutes)
    return `${value}m ago`
  } else if (hours < 24) {
    const value = Math.max(1, hours)
    const label = value === 1 ? 'hr' : 'hrs'
    return `${value}${label} ago`
  } else {
    const value = Math.max(1, days)
    return `${value}d ago`
  }
}

const handleActivityAction = (activity) => {
  // Handle different activity actions
  switch (activity.type) {
    case 'message':
      router.push('/messages')
      break
    case 'job':
      router.push('/jobs')
      break
    case 'connection':
      router.push('/alumni')
      break
    case 'event':
      router.push('/events')
      break
  }
}

const refreshData = async () => {
  if (loading.value) return
  const isAuthenticated = await authStore.checkAuth()
  if (!isAuthenticated) {
    router.push('/login')
    return
  }
  await fetchDashboardData()
}

// Load initial data
onMounted(async () => {
  const isAuthenticated = await authStore.checkAuth()
  if (!isAuthenticated) {
    router.push('/login')
    return
  }
  fetchDashboardData()
})
</script>
