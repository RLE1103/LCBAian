<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">AI Analytics Dashboard</h1>
        <p class="text-gray-600 mt-2">K-Means Clustering and Content-Based Filtering Insights</p>
      </div>
      <div class="flex items-center space-x-4">
        <button 
          @click="runClustering"
          :disabled="isClustering"
          class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
        >
          <svg v-if="isClustering" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
          </svg>
          {{ isClustering ? 'Running Clustering...' : 'Run Clustering' }}
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

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm font-medium">Total Alumni</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.total_users || 0 }}</p>
          </div>
          <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm font-medium">Active Jobs</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.total_jobs || 0 }}</p>
          </div>
          <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm font-medium">Applications</p>
            <p class="text-3xl font-bold">{{ analyticsData.overview?.total_applications || 0 }}</p>
          </div>
          <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-orange-100 text-sm font-medium">Clusters</p>
            <p class="text-3xl font-bold">{{ analyticsData.clustering?.length || 0 }}</p>
          </div>
          <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
        <div v-if="clusterProfiles.length > 0" class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Cluster Profiles</h3>
          <div 
            v-for="profile in clusterProfiles" 
            :key="profile.cluster_id"
            class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer"
            @click="viewClusterDetails(profile.cluster_id)"
          >
            <div class="flex items-center justify-between mb-2">
              <h4 class="font-semibold text-gray-900">Cluster {{ profile.cluster_id }}</h4>
              <span class="bg-gray-100 text-gray-600 text-sm px-2 py-1 rounded-full">
                {{ profile.total_users }} users
              </span>
            </div>
            
            <div class="space-y-2">
              <div v-if="profile.top_skills && Object.keys(profile.top_skills).length > 0">
                <p class="text-sm font-medium text-gray-700">Top Skills:</p>
                <div class="flex flex-wrap gap-1">
                  <span 
                    v-for="(count, skill) in Object.entries(profile.top_skills).slice(0, 3)" 
                    :key="skill"
                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                  >
                    {{ skill }} ({{ count }})
                  </span>
                </div>
              </div>
              
              <div v-if="profile.top_industries && Object.keys(profile.top_industries).length > 0">
                <p class="text-sm font-medium text-gray-700">Top Industries:</p>
                <div class="flex flex-wrap gap-1">
                  <span 
                    v-for="(count, industry) in Object.entries(profile.top_industries).slice(0, 2)" 
                    :key="industry"
                    class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full"
                  >
                    {{ industry }} ({{ count }})
                  </span>
                </div>
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

      <!-- Content-Based Filtering Insights -->
      <div class="space-y-6">
        <!-- Trending Skills -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Trending Skills</h2>
          
          <div v-if="analyticsData.trending_skills && Object.keys(analyticsData.trending_skills).length > 0" class="space-y-3">
            <div 
              v-for="(count, skill) in Object.entries(analyticsData.trending_skills).slice(0, 10)" 
              :key="skill"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <span class="font-medium text-gray-900">{{ skill }}</span>
              <span class="bg-blue-100 text-blue-800 text-sm px-2 py-1 rounded-full">
                {{ count }} jobs
              </span>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <p>No trending skills data</p>
          </div>
        </div>

        <!-- Industry Distribution -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-6">Industry Distribution</h2>
          
          <div v-if="analyticsData.industry_distribution && Object.keys(analyticsData.industry_distribution).length > 0" class="space-y-3">
            <div 
              v-for="(count, industry) in Object.entries(analyticsData.industry_distribution).slice(0, 8)" 
              :key="industry"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <span class="font-medium text-gray-900">{{ industry }}</span>
              <span class="bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">
                {{ count }} alumni
              </span>
            </div>
          </div>
          
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p>No industry data available</p>
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
const isClustering = ref(false)
const showClusterModal = ref(false)
const selectedClusterId = ref(null)
const clusterDetails = ref(null)

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
      
      // Load cluster profiles
      const clusterResponse = await axios.get('/api/admin/analytics/cluster-report')
      if (clusterResponse.data.success) {
        clusterProfiles.value = clusterResponse.data.data.profiles || []
      }
    }
  } catch (error) {
    console.error('Error loading analytics data:', error)
  }
}

const runClustering = async () => {
  isClustering.value = true
  try {
    const response = await axios.post('/api/admin/analytics/run-clustering', {
      clusters: 5
    })
    
    if (response.data.success) {
      // Refresh data after clustering
      await loadAnalyticsData()
      alert('Clustering completed successfully!')
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
