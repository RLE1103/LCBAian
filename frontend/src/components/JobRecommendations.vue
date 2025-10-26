<template>
  <div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Recommended for You</h3>
      <div class="flex items-center space-x-2">
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">
          AI Powered
        </span>
        <button 
          @click="refreshRecommendations"
          :disabled="loading"
          class="text-gray-500 hover:text-gray-700"
        >
          <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
      <p class="text-gray-600">Finding your perfect matches...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="recommendations.length === 0" class="text-center py-8 text-gray-500">
      <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
      </svg>
      <p class="font-medium">No recommendations found</p>
      <p class="text-sm">Add more skills to your profile for better matches!</p>
      <button 
        @click="goToProfile"
        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm"
      >
        Update Profile
      </button>
    </div>

    <!-- Recommendations List -->
    <div v-else class="space-y-4">
      <div 
        v-for="recommendation in recommendations" 
        :key="recommendation.job.job_id"
        class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer"
        @click="viewJobDetails(recommendation.job)"
      >
        <!-- Job Header -->
        <div class="flex items-start justify-between mb-3">
          <div class="flex-1">
            <h4 class="font-semibold text-gray-900 text-sm">{{ recommendation.job.title }}</h4>
            <p class="text-sm text-gray-600">{{ recommendation.job.company_name }}</p>
            <div class="flex items-center gap-2 mt-1">
              <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                {{ recommendation.job.location }}
              </span>
              <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                {{ recommendation.job.work_type }}
              </span>
            </div>
          </div>
          
          <!-- Match Score -->
          <div class="text-right">
            <div class="flex items-center gap-2">
              <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ recommendation.match_percentage }}%
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Match Score</p>
          </div>
        </div>

        <!-- Match Summary -->
        <div class="mb-3">
          <p class="text-sm text-gray-700">{{ recommendation.match_summary }}</p>
        </div>

        <!-- Skills Match -->
        <div class="space-y-2">
          <!-- Matched Skills -->
          <div v-if="recommendation.matched_skills.length > 0">
            <p class="text-xs font-medium text-green-700 mb-1">✓ Matched Skills:</p>
            <div class="flex flex-wrap gap-1">
              <span 
                v-for="skill in recommendation.matched_skills.slice(0, 3)" 
                :key="skill"
                class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full"
              >
                {{ skill }}
              </span>
              <span v-if="recommendation.matched_skills.length > 3" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                +{{ recommendation.matched_skills.length - 3 }} more
              </span>
            </div>
          </div>

          <!-- Missing Skills -->
          <div v-if="recommendation.missing_skills.length > 0">
            <p class="text-xs font-medium text-orange-700 mb-1">⚠ Missing Skills:</p>
            <div class="flex flex-wrap gap-1">
              <span 
                v-for="skill in recommendation.missing_skills.slice(0, 2)" 
                :key="skill"
                class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full"
              >
                {{ skill }}
              </span>
              <span v-if="recommendation.missing_skills.length > 2" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                +{{ recommendation.missing_skills.length - 2 }} more
              </span>
            </div>
          </div>

          <!-- Bonus Skills -->
          <div v-if="recommendation.bonus_skills.length > 0">
            <p class="text-xs font-medium text-blue-700 mb-1">⭐ Bonus Skills:</p>
            <div class="flex flex-wrap gap-1">
              <span 
                v-for="skill in recommendation.bonus_skills.slice(0, 2)" 
                :key="skill"
                class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
              >
                {{ skill }}
              </span>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
          <button 
            @click.stop="applyToJob(recommendation.job)"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium"
          >
            Apply Now
          </button>
          <button 
            @click.stop="viewJobDetails(recommendation.job)"
            class="text-gray-600 hover:text-gray-800 text-sm font-medium"
          >
            View Details
          </button>
        </div>
      </div>
    </div>

    <!-- View More Button -->
    <div v-if="recommendations.length > 0" class="mt-6 text-center">
      <button 
        @click="viewAllRecommendations"
        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
      >
        View All Recommendations →
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../config/api'

const router = useRouter()

// Reactive data
const recommendations = ref([])
const loading = ref(true)

// Methods
const loadRecommendations = async () => {
  loading.value = true
  try {
    const response = await axios.get('/api/jobs/quick-recommendations', {
      params: { limit: 5 }
    })
    
    if (response.data.success) {
      recommendations.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading recommendations:', error)
  } finally {
    loading.value = false
  }
}

const refreshRecommendations = async () => {
  await loadRecommendations()
}

const viewJobDetails = (job) => {
  // Navigate to job details or open modal
  console.log('View job details:', job)
  // You can implement job details modal or navigation here
}

const applyToJob = (job) => {
  // Navigate to application form
  console.log('Apply to job:', job)
  // You can implement application flow here
}

const viewAllRecommendations = () => {
  // Navigate to full recommendations page
  router.push('/jobs?tab=recommended')
}

const goToProfile = () => {
  // Navigate to profile page
  router.push('/profile')
}

// Load recommendations on mount
onMounted(() => {
  loadRecommendations()
})
</script>
