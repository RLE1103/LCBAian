<template>
  <div class="p-4 md:p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 md:mb-8 gap-3">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Alumni Tracker</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1 md:mt-2">Discover dynamic alumni clusters powered by K-Means</p>
      </div>
      <div class="flex items-center space-x-2 md:space-x-4">
        <button 
          @click="showClusteringModal = true"
          class="bg-gray-500 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center gap-2 text-sm md:text-base"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
          </svg>
          Configure Clustering
        </button>
        <button @click="runClustering" :disabled="isClustering" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2">
          <svg v-if="isClustering" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
          {{ isClustering ? 'Discovering...' : 'Discover Groups' }}
        </button>
        <button 
          @click="refreshData"
          class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Overview Stats - At-a-Glance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm font-medium">Total Alumni</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.total_alumni || 0 }}</p>
            <p class="text-blue-100 text-xs mt-1">Overall user base size</p>
          </div>
          <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm font-medium">New Alumni</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.new_alumni_last_30_days || 0 }}</p>
            <p class="text-green-100 text-xs mt-1">Last 30 days</p>
          </div>
          <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm font-medium">Jobs Listed</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.jobs_listed_last_30_days || 0 }}</p>
            <p class="text-purple-100 text-xs mt-1">Last 30 days</p>
          </div>
          <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm font-medium">Events</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.total_events || 0 }}</p>
            <p class="text-orange-100 text-xs mt-1">Community engagement</p>
          </div>
          <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl mx-auto">
      <!-- K-Means Clustering Results -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-xl font-bold text-gray-900">Alumni Clustering (K-Means)</h2>
          <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">
            {{ analyticsData.clustering?.length || 0 }} Clusters
          </span>
        </div>

        <!-- Clustering Chart -->
        <div v-if="analyticsData.clustering?.length > 0" class="mb-6">
          <div class="h-64">
            <Pie :data="clusterChartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Cluster Details -->
        <div v-if="clusterProfiles.length > 0" class="space-y-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Cluster Profiles</h3>
          <div 
            v-for="profile in clusterProfiles" 
            :key="profile.cluster_id"
            class="border border-gray-200 rounded-lg p-6 hover:border-blue-400 hover:shadow-lg transition-all cursor-pointer"
            @click="viewClusterDetails(profile.cluster_id)"
          >
            <div class="flex items-center justify-between mb-4">
              <h4 class="text-xl font-bold text-gray-900">Cluster {{ profile.cluster_id }}</h4>
              <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                {{ profile.total_users }} alumni
              </span>
            </div>
            
            <!-- Distribution Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div class="bg-blue-50 p-3 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Top Program</p>
                <p class="text-lg font-bold text-blue-600 truncate">
                  {{ getTopItem(profile.top_programs) || 'N/A' }}
                </p>
                <p class="text-xs text-gray-500">{{ getTopPercentage(profile.top_programs, profile.total_users) }}% of cluster</p>
              </div>
              
              <div class="bg-green-50 p-3 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Top Industry</p>
                <p class="text-lg font-bold text-green-600 truncate">
                  {{ getTopItem(profile.top_industries) || 'N/A' }}
                </p>
                <p class="text-xs text-gray-500">{{ getTopPercentage(profile.top_industries, profile.total_users) }}% of cluster</p>
              </div>
              
              <div class="bg-purple-50 p-3 rounded-lg">
                <p class="text-xs text-gray-600 mb-1">Top Location</p>
                <p class="text-lg font-bold text-purple-600 truncate">
                  {{ getTopItem(profile.top_cities) || 'N/A' }}
                </p>
                <p class="text-xs text-gray-500">{{ getTopPercentage(profile.top_cities, profile.total_users) }}% of cluster</p>
              </div>
            </div>
            
            <!-- Auto-generated Insight -->
            <div v-if="clusterInsights[profile.cluster_id]" class="bg-gray-50 p-4 rounded-lg mt-4">
              <p class="text-sm text-gray-700">
                <strong class="text-blue-600">Insight:</strong> {{ clusterInsights[profile.cluster_id].insight }}
              </p>
            </div>
            
            <!-- Top Skills -->
            <div v-if="profile.top_skills && Object.keys(profile.top_skills).length > 0" class="mt-4">
              <p class="text-sm font-medium text-gray-700 mb-2">Top Skills:</p>
              <div class="flex flex-wrap gap-1">
                <span 
                  v-for="(count, skill) in Object.entries(profile.top_skills).slice(0, 5)" 
                  :key="skill"
                  class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                >
                  {{ skill }} ({{ count }})
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
          <p>No clustering data available</p>
          <p class="text-sm">Run clustering analysis to see results</p>
        </div>
      </div>
    </div>

    <!-- Clustering Configuration Modal -->
    <div v-if="showClusteringModal" class="fixed inset-0 flex items-center justify-center z-50 p-4" @click.self="showClusteringModal = false">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl border-4 border-gray-300">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Clustering Configuration</h2>
            <button @click="showClusteringModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="space-y-6">
            <!-- Clusters (K) Input -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Number of Clusters (K)</label>
              <input 
                type="number" 
                min="2" 
                max="10" 
                v-model.number="kValue" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
              />
              <p class="text-xs text-gray-500 mt-1">Select the number of clusters to create (2-10)</p>
            </div>

            <!-- Data Parameters -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Data Parameters</label>
              <p class="text-xs text-gray-500 mb-3">Select which fields to use for clustering analysis</p>
              <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.program" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Program</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.graduation_year" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Graduation Year</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.industry" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Current Job Field</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.city" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">City</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.country" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Country</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.employment_status" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Employment Status</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.years_of_experience" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Years of Experience</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.salary_range" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Salary Range</span>
                </label>
                <label class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                  <input type="checkbox" v-model="selectedFields.skills" class="rounded border-gray-300" />
                  <span class="text-sm text-gray-700">Skills</span>
                </label>
              </div>
              <p class="text-xs text-gray-500 mt-3">At least one parameter must be selected</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t">
              <button 
                @click="showClusteringModal = false"
                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
              >
                Cancel
              </button>
              <button 
                @click="applyClusteringConfig"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Apply & Run
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cluster Details Modal -->
    <div v-if="showClusterModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto border-4 border-gray-300 shadow-2xl">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Cluster {{ selectedClusterId }} Details</h2>
            <button @click="showClusterModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div v-if="clusterDetails" class="space-y-6">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900">Total Users</h3>
                <p class="text-2xl font-bold text-blue-600">{{ clusterDetails.total_users }}</p>
              </div>
              <div class="bg-green-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900">Programs</h3>
                <p class="text-2xl font-bold text-green-600">{{ Object.keys(clusterDetails.statistics?.programs || {}).length }}</p>
              </div>
              <div class="bg-purple-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900">Industries</h3>
                <p class="text-2xl font-bold text-purple-600">{{ Object.keys(clusterDetails.statistics?.industries || {}).length }}</p>
              </div>
            </div>

            <!-- Top Skills -->
            <div v-if="clusterDetails.statistics?.top_skills">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Skills</h3>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="(count, skill) in clusterDetails.statistics.top_skills" 
                  :key="skill"
                  class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full"
                >
                  {{ skill }} ({{ count }})
                </span>
              </div>
            </div>

            <!-- Users List -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Users in this Cluster</h3>
              <div class="space-y-2 max-h-64 overflow-y-auto">
                <div 
                  v-for="user in clusterDetails.users" 
                  :key="user.id"
                  class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                >
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                      {{ user.first_name[0] }}{{ user.last_name[0] }}
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ user.first_name }} {{ user.last_name }}</p>
                      <p class="text-sm text-gray-600">{{ user.program }} • {{ user.industry }}</p>
                    </div>
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ user.current_job_title || 'No title' }}
                  </div>
                </div>
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
import { Pie } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'
import axios from '../config/api'

// Register Chart.js components
ChartJS.register(Title, Tooltip, Legend, ArcElement)

// Reactive data
const analyticsData = ref({})
const clusterProfiles = ref([])
const clusterInsights = ref({})
const isClustering = ref(false)
const showClusterModal = ref(false)
const showClusteringModal = ref(false)
const selectedClusterId = ref(null)
const clusterDetails = ref(null)
const kValue = ref(4)
const selectedFields = ref({ program: true, graduation_year: false, industry: true, city: true, country: true, employment_status: false, years_of_experience: false, salary_range: false, skills: false })

// Chart options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          const label = context.label || ''
          const value = context.parsed
          const total = context.dataset.data.reduce((a, b) => a + b, 0)
          const percentage = ((value / total) * 100).toFixed(1)
          return `${label}: ${value} users (${percentage}%)`
        }
      }
    }
  }
}

// Computed properties
const clusterChartData = computed(() => {
  if (!analyticsData.value.clustering || analyticsData.value.clustering.length === 0) {
    return {
      labels: [],
      datasets: [{
        data: [],
        backgroundColor: []
      }]
    }
  }

  const labels = analyticsData.value.clustering.map(cluster => `Cluster ${cluster.cluster_id}`)
  const data = analyticsData.value.clustering.map(cluster => cluster.count)
  const colors = [
    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
    '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
  ]

  return {
    labels: labels,
    datasets: [{
      data: data,
      backgroundColor: colors.slice(0, labels.length),
      borderWidth: 2,
      borderColor: '#ffffff'
    }]
  }
})

// Methods
const loadAnalyticsData = async () => {
  try {
    const response = await axios.get('/api/admin/analytics/dashboard')
    if (response.data.success) {
      analyticsData.value = response.data.data
      clusterInsights.value = response.data.data.cluster_insights || {}
      
      // Load cluster profiles
      const clusterResponse = await axios.get('/api/admin/analytics/cluster-report')
      if (clusterResponse.data.success) {
        clusterProfiles.value = clusterResponse.data.data.profiles || []
      }
    }
  } catch (error) {
    console.error('Error loading analytics data:', error)
    alert('Error loading analytics data: ' + error.message)
  }
}

// Helper methods for cluster display
const getTopItem = (itemsObj) => {
  if (!itemsObj || Object.keys(itemsObj).length === 0) return null
  return Object.keys(itemsObj)[0]
}

const getTopPercentage = (itemsObj, totalUsers) => {
  if (!itemsObj || Object.keys(itemsObj).length === 0 || !totalUsers) return 0
  const topItem = Object.keys(itemsObj)[0]
  const count = itemsObj[topItem]
  return Math.round((count / totalUsers) * 100)
}

const runClustering = async () => {
  // Validate that at least one field is selected
  const selectedFieldsList = Object.keys(selectedFields.value).filter(k => selectedFields.value[k])
  if (selectedFieldsList.length === 0) {
    alert('Please select at least one data parameter')
    showClusteringModal.value = true
    return
  }

  isClustering.value = true
  try {
    const response = await axios.post('/api/admin/analytics/run-clustering', {
      clusters: kValue.value,
      fields: selectedFieldsList
    })
    
    if (response.data.success) {
      // If backend returns fresh analytics/profiles, apply immediately
      const payload = response.data.data
      if (payload && payload.analytics && payload.profiles) {
        analyticsData.value = {
          ...(analyticsData.value || {}),
          clustering: payload.analytics
        }
        clusterProfiles.value = payload.profiles || []
      } else {
        // Fallback: refresh via API
        await loadAnalyticsData()
      }
      alert('Clustering completed successfully!')
      showClusteringModal.value = false
    } else {
      alert('Clustering failed: ' + response.data.message)
    }
  } catch (error) {
    console.error('Error running clustering:', error)
    alert('Error running clustering: ' + error.message)
  } finally {
    isClustering.value = false
  }
}

const applyClusteringConfig = () => {
  // Validate that at least one field is selected
  const selectedFieldsList = Object.keys(selectedFields.value).filter(k => selectedFields.value[k])
  if (selectedFieldsList.length === 0) {
    alert('Please select at least one data parameter')
    return
  }
  showClusteringModal.value = false
  runClustering()
}

const refreshData = async () => {
  await loadAnalyticsData()
}

const viewClusterDetails = async (clusterId) => {
  selectedClusterId.value = clusterId
  showClusterModal.value = true
  
  try {
    const response = await axios.get('/api/admin/analytics/cluster-details', {
      params: { cluster_id: clusterId }
    })
    
    if (response.data.success) {
      clusterDetails.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading cluster details:', error)
  }
}

// Load data on mount
onMounted(() => {
  loadAnalyticsData()
})
</script>
