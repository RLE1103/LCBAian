<template>
  <div class="p-6">
    <!-- Welcome Header -->
    <div class="mb-8">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ userFullName }}!</h1>
          <p class="text-gray-600 mt-2">Here's what's happening in your LCBAian network today</p>
        </div>
        <div class="flex items-center space-x-4">
          <button @click="refreshData" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Refresh
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <RouterLink to="/messages" class="group">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white hover:from-blue-600 hover:to-blue-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-blue-100 text-sm font-medium">Messages</p>
              <p class="text-3xl font-bold">{{ stats.messages }}</p>
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
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white hover:from-green-600 hover:to-green-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-green-100 text-sm font-medium">Job Applications</p>
              <p class="text-3xl font-bold">{{ stats.jobApplications }}</p>
              <p class="text-green-200 text-xs">{{ stats.pendingApplications }} pending</p>
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
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white hover:from-purple-600 hover:to-purple-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-purple-100 text-sm font-medium">Connections</p>
              <p class="text-3xl font-bold">{{ stats.connections }}</p>
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
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white hover:from-orange-600 hover:to-orange-700 transition-all duration-200 transform group-hover:scale-105">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-orange-100 text-sm font-medium">Events</p>
              <p class="text-3xl font-bold">{{ stats.events }}</p>
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - Recent Activity & Notifications -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Recent Activity</h2>
            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</button>
          </div>
          <div class="space-y-4">
            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <div class="flex-shrink-0">
                <div :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm',
                  activity.type === 'message' ? 'bg-blue-500' :
                  activity.type === 'job' ? 'bg-green-500' :
                  activity.type === 'connection' ? 'bg-purple-500' :
                  'bg-orange-500'
                ]">
                  {{ activity.icon }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
                <p class="text-sm text-gray-600">{{ activity.description }}</p>
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

            <RouterLink to="/mentorship" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-green-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-green-600">Mentorship</h3>
                    <p class="text-sm text-gray-600">Find or offer guidance</p>
                  </div>
                </div>
              </div>
            </RouterLink>

            <RouterLink to="/communities" class="group">
              <div class="p-4 border border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all duration-200">
                <div class="flex items-center space-x-3">
                  <div class="bg-orange-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-medium text-gray-900 group-hover:text-orange-600">Communities</h3>
                    <p class="text-sm text-gray-600">Join discussions</p>
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

const authStore = useAuthStore()
const router = useRouter()

// User data
const userFullName = computed(() => authStore.fullName || 'User')

// Dashboard statistics
const stats = ref({
  messages: 12,
  unreadMessages: 3,
  jobApplications: 8,
  pendingApplications: 2,
  connections: 156,
  newConnections: 5,
  events: 4,
  upcomingEvents: 2
})

// Recent activity data
const recentActivity = ref([
  {
    id: 1,
    type: 'message',
    icon: '💬',
    title: 'New message from John Doe',
    description: 'Thanks for the job referral!',
    timestamp: new Date(Date.now() - 30 * 60 * 1000), // 30 minutes ago
    action: 'Reply'
  },
  {
    id: 2,
    type: 'job',
    icon: '💼',
    title: 'Application status updated',
    description: 'Software Engineer at Google - Under Review',
    timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
    action: 'View'
  },
  {
    id: 3,
    type: 'connection',
    icon: '👥',
    title: 'New connection request',
    description: 'Sarah Martinez wants to connect',
    timestamp: new Date(Date.now() - 4 * 60 * 60 * 1000), // 4 hours ago
    action: 'Accept'
  },
  {
    id: 4,
    type: 'event',
    icon: '📅',
    title: 'Event reminder',
    description: 'LCBA Alumni Meetup 2024 starts in 2 days',
    timestamp: new Date(Date.now() - 6 * 60 * 60 * 1000), // 6 hours ago
    action: 'RSVP'
  }
])

// Notifications data
const notifications = ref([
  {
    id: 1,
    title: 'New Job Match',
    message: '3 new jobs match your profile',
    timestamp: new Date(Date.now() - 15 * 60 * 1000), // 15 minutes ago
    read: false
  },
  {
    id: 2,
    title: 'Mentorship Request',
    message: 'Someone requested you as a mentor',
    timestamp: new Date(Date.now() - 1 * 60 * 60 * 1000), // 1 hour ago
    read: false
  },
  {
    id: 3,
    title: 'Event Update',
    message: 'LCBA Tech Conference schedule updated',
    timestamp: new Date(Date.now() - 3 * 60 * 60 * 1000), // 3 hours ago
    read: true
  },
  {
    id: 4,
    title: 'Connection Accepted',
    message: 'Mike Johnson accepted your connection request',
    timestamp: new Date(Date.now() - 5 * 60 * 60 * 1000), // 5 hours ago
    read: true
  }
])

// Upcoming events
const upcomingEvents = ref([
  {
    id: 1,
    title: 'LCBA Alumni Meetup 2024',
    date: 'Dec 15, 2024',
    time: '6:00 PM',
    location: 'Manila Hotel'
  },
  {
    id: 2,
    title: 'Tech Career Workshop',
    date: 'Dec 20, 2024',
    time: '2:00 PM',
    location: 'Online'
  },
  {
    id: 3,
    title: 'Networking Mixer',
    date: 'Dec 28, 2024',
    time: '7:00 PM',
    location: 'Makati City'
  }
])

// Network updates
const networkUpdates = ref([
  {
    id: 1,
    user: {
      first_name: 'John',
      last_name: 'Doe',
      full_name: 'John Doe'
    },
    action: 'got a new job at Microsoft',
    timestamp: new Date(Date.now() - 1 * 60 * 60 * 1000) // 1 hour ago
  },
  {
    id: 2,
    user: {
      first_name: 'Sarah',
      last_name: 'Martinez',
      full_name: 'Sarah Martinez'
    },
    action: 'shared a job posting',
    timestamp: new Date(Date.now() - 3 * 60 * 60 * 1000) // 3 hours ago
  },
  {
    id: 3,
    user: {
      first_name: 'Mike',
      last_name: 'Johnson',
      full_name: 'Mike Johnson'
    },
    action: 'became a mentor',
    timestamp: new Date(Date.now() - 6 * 60 * 60 * 1000) // 6 hours ago
  }
])

// Methods
const formatTime = (timestamp) => {
  const now = new Date()
  const diff = now - timestamp
  const minutes = Math.floor(diff / (1000 * 60))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))

  if (minutes < 60) {
    return `${minutes}m ago`
  } else if (hours < 24) {
    return `${hours}h ago`
  } else {
    return `${days}d ago`
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

const refreshData = () => {
  // Simulate data refresh
  console.log('Refreshing dashboard data...')
  // In a real app, this would fetch fresh data from the API
}

// Load initial data
onMounted(() => {
  // Load dashboard data
  console.log('Dashboard mounted, loading data...')
})
</script>


