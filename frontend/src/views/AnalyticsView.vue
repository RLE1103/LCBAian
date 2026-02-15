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
          {{ isClustering ? 'Running...' : 'Run Clustering' }}
        </button>
        <button 
          @click="refreshData"
          :disabled="isRefreshing"
          class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        >
          <svg v-if="isRefreshing" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          {{ isRefreshing ? 'Refreshing...' : 'Refresh' }}
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="text-center py-12 text-gray-500">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p>Loading analytics...</p>
    </div>
    <div v-else>
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
              <h4 class="text-xl font-bold text-gray-900">{{ getClusterTitle(profile) }}</h4>
              <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                {{ profile.total_users }} alumni
              </span>
            </div>
            
            <div v-if="getMetricCards(profile).length > 0" :class="['grid gap-4 mb-4', getGridClass(getMetricCards(profile).length)]">
              <div
                v-for="card in getMetricCards(profile)"
                :key="card.key"
                :class="['rounded-lg border p-4', card.containerClass]"
              >
                <div class="flex items-start gap-3">
                  <div :class="['rounded-full p-2 shrink-0', card.iconClass]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path v-for="(d, index) in (iconPathMap[card.icon] || iconPathMap.default)" :key="index" :d="d"></path>
                    </svg>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-600">{{ card.label }}</p>
                    <p :class="['text-lg font-bold truncate', card.valueClass]">{{ card.value }}</p>
                    <p v-if="card.subtext" class="text-xs text-gray-500 mt-1">{{ card.subtext }}</p>
                    <div v-if="card.tags && card.tags.length" class="flex flex-wrap gap-1 mt-2">
                      <span v-for="tag in card.tags" :key="tag.label" :class="['text-xs px-2 py-1 rounded-full', card.tagClass]">
                        {{ tag.label }} ({{ tag.count }})
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Auto-generated Insight -->
            <div v-if="getInsightText(profile)" class="bg-gray-50 p-4 rounded-lg mt-4">
              <p class="text-sm text-gray-700">
                <strong class="text-blue-600">Insight:</strong> {{ getInsightText(profile) }}
              </p>
              <div v-if="getCareerMatchSuggestion(profile)" class="mt-3 text-sm text-amber-700 flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3a7 7 0 00-4 12c.6.5 1 1.5 1 2.5V19a1 1 0 001 1h4a1 1 0 001-1v-1.5c0-1 .4-2 1-2.5a7 7 0 00-4-12z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21h6"></path>
                </svg>
                <div>
                  <p class="font-semibold text-amber-800">Career Match Suggestion</p>
                  <p class="text-amber-700">{{ getCareerMatchSuggestion(profile) }}</p>
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
    </div>

    <!-- Clustering Configuration Modal -->
    <div v-if="showClusteringModal" class="fixed inset-0 bg-transparent flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showClusteringModal = false">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto shadow-2xl border-2 border-black">
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
    <div v-if="showClusterModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showClusterModal = false">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto border-2 border-black shadow-2xl">
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
const isLoading = ref(false)
const isClustering = ref(false)
const isRefreshing = ref(false)
const showClusterModal = ref(false)
const showClusteringModal = ref(false)
const selectedClusterId = ref(null)
const clusterDetails = ref(null)
const kValue = ref(4)
const selectedFields = ref({ program: true, graduation_year: false, industry: true, city: true, country: true, employment_status: false, years_of_experience: false, salary_range: false, skills: false })
const activeFields = ref(Object.keys(selectedFields.value).filter(k => selectedFields.value[k]))

const iconPathMap = {
  graduationCap: [
    'M22 10l-10-5-10 5 10 5 10-5z',
    'M6 12v5a6 6 0 0 0 12 0v-5',
    'M4 12v5'
  ],
  calendar: [
    'M8 2v4',
    'M16 2v4',
    'M3 10h18',
    'M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z'
  ],
  globe: [
    'M12 2a10 10 0 1 0 0 20a10 10 0 0 0 0-20z',
    'M2 12h20',
    'M12 2a15.3 15.3 0 0 1 4 10a15.3 15.3 0 0 1-4 10a15.3 15.3 0 0 1-4-10a15.3 15.3 0 0 1 4-10z'
  ],
  briefcase: [
    'M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2',
    'M3 7h18a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z',
    'M16 13H8'
  ],
  star: [
    'M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z'
  ],
  clock: [
    'M12 2a10 10 0 1 0 0 20a10 10 0 0 0 0-20z',
    'M12 6v6l4 2'
  ],
  dollar: [
    'M12 1v22',
    'M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6'
  ],
  book: [
    'M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z',
    'M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z'
  ],
  factory: [
    'M2 20h20',
    'M6 20V8l6 4V8l6 4v8',
    'M6 8V4h4v4'
  ],
  mapPin: [
    'M12 21s6-4.35 6-9a6 6 0 1 0-12 0c0 4.65 6 9 6 9z',
    'M12 11a2 2 0 1 0 0-4a2 2 0 0 0 0 4z'
  ],
  map: [
    'M9 18l-6 3V6l6-3 6 3 6-3v15l-6 3-6-3z',
    'M9 3v15',
    'M15 6v15'
  ],
  default: [
    'M12 2v20',
    'M2 12h20'
  ]
}

const toneMap = {
  program: { container: 'bg-blue-50 border-blue-100', icon: 'bg-blue-100 text-blue-700', value: 'text-blue-700', tag: 'bg-blue-100 text-blue-800' },
  industry: { container: 'bg-green-50 border-green-100', icon: 'bg-green-100 text-green-700', value: 'text-green-700', tag: 'bg-green-100 text-green-800' },
  graduation_year: { container: 'bg-purple-50 border-purple-100', icon: 'bg-purple-100 text-purple-700', value: 'text-purple-700', tag: 'bg-purple-100 text-purple-800' },
  country: { container: 'bg-sky-50 border-sky-100', icon: 'bg-sky-100 text-sky-700', value: 'text-sky-700', tag: 'bg-sky-100 text-sky-800' },
  city: { container: 'bg-indigo-50 border-indigo-100', icon: 'bg-indigo-100 text-indigo-700', value: 'text-indigo-700', tag: 'bg-indigo-100 text-indigo-800' },
  location: { container: 'bg-violet-50 border-violet-100', icon: 'bg-violet-100 text-violet-700', value: 'text-violet-700', tag: 'bg-violet-100 text-violet-800' },
  employment_status: { container: 'bg-amber-50 border-amber-100', icon: 'bg-amber-100 text-amber-700', value: 'text-amber-700', tag: 'bg-amber-100 text-amber-800' },
  years_of_experience: { container: 'bg-teal-50 border-teal-100', icon: 'bg-teal-100 text-teal-700', value: 'text-teal-700', tag: 'bg-teal-100 text-teal-800' },
  salary_range: { container: 'bg-rose-50 border-rose-100', icon: 'bg-rose-100 text-rose-700', value: 'text-rose-700', tag: 'bg-rose-100 text-rose-800' },
  skills: { container: 'bg-cyan-50 border-cyan-100', icon: 'bg-cyan-100 text-cyan-700', value: 'text-cyan-700', tag: 'bg-cyan-100 text-cyan-800' },
  default: { container: 'bg-gray-50 border-gray-100', icon: 'bg-gray-100 text-gray-700', value: 'text-gray-700', tag: 'bg-gray-100 text-gray-800' }
}

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
const loadAnalyticsData = async (options = {}) => {
  if (!options.refresh) {
    isLoading.value = true
  }
  try {
    const params = options.refresh ? { refresh: Date.now() } : undefined
    const response = await axios.get('/api/admin/analytics/dashboard', { params })
    if (response.data.success) {
      analyticsData.value = response.data.data
      clusterInsights.value = response.data.data.cluster_insights || {}
      
      const clusterResponse = await axios.get('/api/admin/analytics/cluster-report', { params })
      if (clusterResponse.data.success) {
        clusterProfiles.value = clusterResponse.data.data.profiles || []
      }
    }
  } catch (error) {
    console.error('Error loading analytics data:', error)
    alert('Error loading analytics data: ' + error.message)
  } finally {
    if (!options.refresh) {
      isLoading.value = false
    }
  }
}

// Helper methods for cluster display
const getTopItem = (itemsObj) => {
  if (!itemsObj || Object.keys(itemsObj).length === 0) return null
  return Object.keys(itemsObj)[0]
}

const getTopSkill = (profile, index = 0) => {
  const skills = profile?.top_skills ? Object.keys(profile.top_skills) : []
  return skills[index] || null
}

const hasActiveField = (field) => activeFields.value.includes(field)

const hasLocationFields = computed(() => {
  return activeFields.value.includes('city') || activeFields.value.includes('country') || activeFields.value.includes('location')
})

const getClusterTitle = (profile) => {
  const topIndustry = hasActiveField('industry') ? getTopItem(profile?.top_industries) : null
  const topProgram = hasActiveField('program') ? getTopItem(profile?.top_programs) : null
  if (topIndustry && topProgram) {
    return `Cluster ${profile.cluster_id}: ${topIndustry}/${topProgram}`
  }
  if (topIndustry) {
    return `Cluster ${profile.cluster_id}: ${topIndustry}`
  }
  if (topProgram) {
    return `Cluster ${profile.cluster_id}: ${topProgram}`
  }
  return `Cluster ${profile.cluster_id}`
}

const getCareerMatchSuggestion = (profile) => {
  const industryActive = hasActiveField('industry')
  const programActive = hasActiveField('program')
  const skillsActive = hasActiveField('skills')
  const topIndustry = industryActive ? getTopItem(profile?.top_industries) : null
  const topProgram = programActive ? getTopItem(profile?.top_programs) : null
  const domain = topIndustry || topProgram
  if (!domain) {
    return ''
  }
  if (!skillsActive) {
    return `Based on this cluster’s profile, people in this group are highly compatible with ${domain} roles.`
  }
  const topSkill1 = getTopSkill(profile, 0)
  const topSkill2 = getTopSkill(profile, 1) || topSkill1
  if (!topSkill1) {
    return `Based on this cluster’s profile, people in this group are highly compatible with ${domain} roles.`
  }
  return `Based on this cluster’s profile, people in this group are highly compatible with ${domain} roles like ${topSkill1} Developer or ${topSkill2} Specialist.`
}

const getInsightText = (profile) => {
  if (!profile) return ''
  const totalUsers = profile.total_users || 0
  const clauses = []
  if (totalUsers > 0) {
    const topCity = hasActiveField('city') ? getTopItem(profile.top_cities) : null
    const topCountry = hasActiveField('country') ? getTopItem(profile.top_countries) : null
    const topLocation = hasActiveField('location') ? getTopItem(profile.top_locations) : null
    let locationPhrase = ''
    if (topCity && topCountry) {
      locationPhrase = `from ${topCity}, ${topCountry}`
    } else if (topCity) {
      locationPhrase = `from ${topCity}`
    } else if (topCountry) {
      locationPhrase = `from ${topCountry}`
    } else if (topLocation) {
      locationPhrase = `from ${topLocation}`
    }
    clauses.push(`This cluster represents ${totalUsers} alumni${locationPhrase ? ` ${locationPhrase}` : ''}`)
  }
  if (hasActiveField('graduation_year')) {
    const topBatch = getTopItem(profile.top_batches)
    if (topBatch) {
      clauses.push(`mostly Class of ${topBatch}`)
    }
  }
  if (hasActiveField('program')) {
    const topProgram = getTopItem(profile.top_programs)
    if (topProgram) {
      clauses.push(`from the ${topProgram} program`)
    }
  }
  if (hasActiveField('industry')) {
    const topIndustry = getTopItem(profile.top_industries)
    if (topIndustry) {
      clauses.push(`working in ${topIndustry}`)
    }
  }
  if (hasActiveField('employment_status')) {
    const topStatus = getTopItem(profile.top_employment_statuses)
    if (topStatus) {
      clauses.push(`primarily ${topStatus}`)
    }
  }
  if (hasActiveField('years_of_experience')) {
    const topExperience = getTopItem(profile.top_years_of_experience)
    if (topExperience) {
      clauses.push(`with ${topExperience} years of experience`)
    }
  }
  if (hasActiveField('salary_range')) {
    const topSalary = getTopItem(profile.top_salary_ranges)
    if (topSalary) {
      clauses.push(`earning ${topSalary}`)
    }
  }
  if (hasActiveField('skills')) {
    const skillList = profile.top_skills ? Object.keys(profile.top_skills).slice(0, 3) : []
    if (skillList.length > 0) {
      clauses.push(`top skills include ${skillList.join(', ')}`)
    }
  }
  return clauses.length > 0 ? `${clauses.join(', ')}.` : ''
}

const getTopPercentage = (itemsObj, totalUsers) => {
  if (!itemsObj || Object.keys(itemsObj).length === 0 || !totalUsers) return 0
  const topItem = Object.keys(itemsObj)[0]
  const count = itemsObj[topItem]
  return Math.round((count / totalUsers) * 100)
}

const getTopLocation = (profile) => {
  if (!profile) return null
  const source = (profile.top_cities && Object.keys(profile.top_cities).length > 0)
    ? profile.top_cities
    : (profile.top_locations && Object.keys(profile.top_locations).length > 0)
      ? profile.top_locations
      : (profile.top_countries && Object.keys(profile.top_countries).length > 0)
        ? profile.top_countries
        : null
  if (!source) return null
  return Object.keys(source)[0]
}

const getTopLocationPercentage = (profile) => {
  if (!profile || !profile.total_users) return 0
  const source = (profile.top_cities && Object.keys(profile.top_cities).length > 0)
    ? profile.top_cities
    : (profile.top_locations && Object.keys(profile.top_locations).length > 0)
      ? profile.top_locations
      : (profile.top_countries && Object.keys(profile.top_countries).length > 0)
        ? profile.top_countries
        : null
  if (!source) return 0
  const topItem = Object.keys(source)[0]
  const count = source[topItem] || 0
  return Math.round((count / profile.total_users) * 100)
}

const getTone = (key) => toneMap[key] || toneMap.default

const parameterDefinitions = [
  { key: 'program', label: 'Program', icon: 'graduationCap', responseKeys: ['top_programs', 'top_program'] },
  { key: 'city', label: 'City', icon: 'mapPin', responseKeys: ['top_cities', 'top_city'] },
  { key: 'country', label: 'Country', icon: 'globe', responseKeys: ['top_countries', 'top_country'] },
  { key: 'industry', label: 'Current Job Field', icon: 'briefcase', responseKeys: ['top_industries', 'top_industry'] },
  { key: 'graduation_year', label: 'Graduation Year', icon: 'calendar', responseKeys: ['top_batches', 'top_graduation_years', 'top_graduation_year'] },
  { key: 'employment_status', label: 'Employment Status', icon: 'briefcase', responseKeys: ['top_employment_statuses', 'top_employment_status'] },
  { key: 'salary_range', label: 'Salary Range', icon: 'dollar', responseKeys: ['top_salary_ranges', 'top_salary_range'] },
  { key: 'years_of_experience', label: 'Years of Experience', icon: 'clock', responseKeys: ['top_years_of_experience', 'top_years_of_experience_level'] },
  { key: 'location', label: 'Location', icon: 'map', responseKeys: ['top_locations', 'top_location'] }
]

const fieldAliasMap = {
  current_job_field: 'industry',
  job_field: 'industry',
  job_industry: 'industry',
  graduation: 'graduation_year',
  batch: 'graduation_year',
  top_city: 'city',
  top_country: 'country',
  top_program: 'program'
}

const normalizeFieldKey = (key) => fieldAliasMap[key] || key

const getSourceObject = (profile, keys) => {
  for (const key of keys) {
    const value = profile?.[key]
    if (value !== undefined && value !== null) {
      return value
    }
  }
  return null
}

const getValueFromSource = (source) => {
  if (!source) return null
  if (typeof source === 'string' || typeof source === 'number') {
    return String(source)
  }
  if (Array.isArray(source)) {
    return source.length > 0 ? String(source[0]) : null
  }
  if (typeof source === 'object') {
    return getTopItem(source)
  }
  return null
}

const getActiveKeys = (profile) => {
  const selected = activeFields.value.length > 0 ? activeFields.value.map(normalizeFieldKey) : []
  const keysFromProfile = parameterDefinitions
    .filter(def => def.responseKeys.some(key => profile?.[key] !== undefined))
    .map(def => def.key)
  const base = selected.length > 0 ? selected : keysFromProfile
  const fallback = ['program', 'city', 'country', 'industry']
  const keys = base.length > 0 ? base : fallback
  const validKeys = new Set(parameterDefinitions.map(def => def.key))
  const normalized = keys.map(normalizeFieldKey).filter(key => validKeys.has(key) || key === 'skills')
  return Array.from(new Set(normalized.length > 0 ? normalized : fallback))
}

const getMetricCards = (profile) => {
  if (!profile) return []
  const totalUsers = profile.total_users || 0
  const cards = []
  const addCard = (key, label, value, sourceObj, icon, tags = []) => {
    const tone = getTone(key)
    const pct = sourceObj && typeof sourceObj === 'object' && !Array.isArray(sourceObj) && value !== 'N/A'
      ? getTopPercentage(sourceObj, totalUsers)
      : 0
    cards.push({
      key,
      label,
      value,
      subtext: pct > 0 ? `${pct}% of cluster` : '',
      icon,
      containerClass: tone.container,
      iconClass: tone.icon,
      valueClass: tone.value,
      tagClass: tone.tag,
      tags
    })
  }

  const activeKeys = getActiveKeys(profile)
  parameterDefinitions.forEach(def => {
    if (!activeKeys.includes(def.key)) return
    const source = getSourceObject(profile, def.responseKeys)
    const rawValue = getValueFromSource(source)
    const formattedValue = def.key === 'graduation_year' && rawValue ? `Class of ${rawValue}` : rawValue
    addCard(def.key, def.label, formattedValue || 'N/A', source, def.icon)
  })

  if (activeKeys.includes('skills') && hasActiveField('skills')) {
    const skills = profile.top_skills ? Object.entries(profile.top_skills).slice(0, 5).map(([label, count]) => ({ label, count })) : []
    const value = skills.length > 0 ? skills[0].label : 'N/A'
    addCard('skills', 'Skills', value, profile.top_skills || null, 'star', skills)
  }
  return cards
}

const getGridClass = (count) => {
  if (count <= 1) return 'grid-cols-1'
  if (count === 2) return 'grid-cols-1 md:grid-cols-2'
  if (count === 3) return 'grid-cols-1 md:grid-cols-3'
  if (count === 4) return 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'
  if (count === 5) return 'grid-cols-1 md:grid-cols-3 lg:grid-cols-5'
  return 'grid-cols-1 md:grid-cols-3 lg:grid-cols-6'
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
        if (payload.insights) {
          clusterInsights.value = payload.insights
        }
        if (payload.active_fields) {
          activeFields.value = payload.active_fields
        } else {
          activeFields.value = selectedFieldsList
        }
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
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await loadAnalyticsData({ refresh: true })
  } finally {
    isRefreshing.value = false
  }
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
