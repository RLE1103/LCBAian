<template>
  <div class="p-4 md:p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">All Career Matches</h1>
        <p class="text-sm text-gray-600 mt-1">Personalized job recommendations based on your profile</p>
      </div>
      <button
        @click="loadCareerMatches"
        :disabled="matchingLoading"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
      >
        {{ matchingLoading ? 'Refreshing...' : 'Refresh' }}
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div v-if="matchingLoading" class="text-center py-12">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600 text-sm">Finding your perfect matches...</p>
      </div>

      <div v-else-if="careerMatches.length === 0" class="text-center py-12 text-gray-500">
        <p class="font-medium text-sm">No matches found</p>
        <p class="text-xs mt-1">Add more skills to your profile for better matches!</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="match in careerMatches"
          :key="match.job?.job_id || match.job_id"
          class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors"
        >
          <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="flex-1">
              <h4 class="font-semibold text-gray-900 text-base">{{ match.job?.title || match.title }}</h4>
              <p class="text-sm text-gray-600">{{ match.job?.company_name || match.company_name }}</p>
              <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                  {{ match.job?.location || match.location }}
                </span>
                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                  {{ match.job?.work_type || match.work_type }}
                </span>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <div
                class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm"
                :style="{ backgroundColor: getMatchStrength(match).bgColor }"
              >
                <span class="sr-only">{{ getMatchStrength(match).label }}</span>
              </div>
              <div class="text-right">
                <p class="text-xs font-semibold" :style="{ color: getMatchStrength(match).textColor }">
                  {{ getMatchStrength(match).label }}
                </p>
              </div>
            </div>
          </div>

          <div v-if="match.match_summary" class="mt-3">
            <p class="text-sm text-gray-700">{{ match.match_summary }}</p>
          </div>

          <div class="mt-4 space-y-2">
            <div v-if="match.matched_skills && match.matched_skills.length > 0">
              <p class="text-xs font-medium text-green-700 mb-1">✓ Matched Skills:</p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="skill in match.matched_skills.slice(0, 6)"
                  :key="skill"
                  class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full"
                >
                  {{ skill }}
                </span>
              </div>
            </div>

            <div v-if="match.missing_skills && match.missing_skills.length > 0">
              <p class="text-xs font-medium text-orange-700 mb-1">⚠ Missing Skills:</p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="skill in match.missing_skills.slice(0, 6)"
                  :key="skill"
                  class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full"
                >
                  {{ skill }}
                </span>
              </div>
            </div>

            <div v-if="match.bonus_skills && match.bonus_skills.length > 0">
              <p class="text-xs font-medium text-blue-700 mb-1">⭐ Bonus Skills:</p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="skill in match.bonus_skills.slice(0, 6)"
                  :key="skill"
                  class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                >
                  {{ skill }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100">
            <button
              @click="selectJob(match)"
              class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium"
            >
              View Details
            </button>
            <button
              @click="toggleSaveJob(match.job || match)"
              :title="isJobSaved(match.job || match) ? 'Unsave' : 'Save'"
              class="text-gray-600 hover:text-gray-800 text-sm font-medium"
            >
              {{ isJobSaved(match.job || match) ? 'Saved' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showJobModal" class="fixed inset-0 bg-transparent flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showJobModal = false">
      <div class="bg-white rounded-lg shadow-xl border-2 border-black w-full max-w-3xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ selectedJob?.title }}</h2>
            <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-gray-600">
              <span class="font-medium">{{ selectedJob?.company_name }}</span>
              <span>{{ selectedJob?.location }}</span>
              <span v-if="selectedJob?.created_at">{{ formatTime(selectedJob.created_at) }}</span>
            </div>
            <div v-if="selectedMatch || selectedJob?.similarity_score !== undefined || selectedJob?.match_percentage !== undefined" class="mt-3 flex items-center gap-2">
              <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: getMatchStrength(selectedMatch || selectedJob).bgColor }"></span>
              <span class="text-sm font-medium" :style="{ color: getMatchStrength(selectedMatch || selectedJob).textColor }">
                {{ getMatchStrength(selectedMatch || selectedJob).label }}
              </span>
            </div>
          </div>
          <button @click="showJobModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
            <p class="text-gray-700 whitespace-pre-wrap">{{ selectedJob?.description }}</p>
          </div>

          <div v-if="selectedJob?.required_skills?.length">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Required Skills</h3>
            <ul class="list-disc pl-5 text-gray-700">
              <li v-for="skill in selectedJob.required_skills" :key="skill">{{ skill }}</li>
            </ul>
          </div>

          <div v-if="selectedJob?.preferred_skills?.length">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Preferred Skills</h3>
            <ul class="list-disc pl-5 text-gray-700">
              <li v-for="skill in selectedJob.preferred_skills" :key="skill">{{ skill }}</li>
            </ul>
          </div>

          <div v-if="selectedJob?.application_link" class="pt-2">
            <a
              :href="selectedJob.application_link"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Apply Now
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../config/api'

const careerMatches = ref([])
const matchingLoading = ref(false)
const savedJobs = ref([])
const selectedJob = ref(null)
const selectedMatch = ref(null)
const showJobModal = ref(false)

const getMatchPercentValue = (match) => {
  if (match?.match_percentage !== undefined) {
    return Number(match.match_percentage)
  }
  if (match?.similarity_score !== undefined) {
    return Math.round(match.similarity_score * 100)
  }
  if (match?.job?.similarity_score !== undefined) {
    return Math.round(match.job.similarity_score * 100)
  }
  return 0
}

const getMatchStrength = (match) => {
  const percentage = getMatchPercentValue(match)
  if (percentage >= 80) {
    return { label: 'Excellent Match', textColor: '#166534', bgColor: '#22c55e' }
  }
  if (percentage >= 60) {
    return { label: 'Strong Match', textColor: '#166534', bgColor: '#4ade80' }
  }
  if (percentage >= 40) {
    return { label: 'Good Match', textColor: '#556b2f', bgColor: '#b5e157' }
  }
  if (percentage >= 20) {
    return { label: 'Fair Match', textColor: '#7a6f2b', bgColor: '#edeb5f' }
  }
  return { label: 'Potential Match', textColor: '#6b7280', bgColor: '#9ca3af' }
}

const loadCareerMatches = async () => {
  matchingLoading.value = true
  try {
    const response = await axios.get('/api/jobs/recommended', {
      params: { limit: 50 }
    })
    if (response.data.success) {
      careerMatches.value = response.data.data || []
    } else {
      careerMatches.value = []
    }
  } catch (error) {
    console.error('Error loading career matches:', error)
    careerMatches.value = []
  } finally {
    matchingLoading.value = false
  }
}

const loadSavedJobs = async () => {
  try {
    const response = await axios.get('/api/saved-jobs')
    if (response?.data?.success) {
      savedJobs.value = response.data.data || []
    } else {
      savedJobs.value = []
    }
  } catch (error) {
    console.error('Error loading saved jobs:', error)
    savedJobs.value = []
  }
}

const isJobSaved = (job) => savedJobs.value.some(j => j.job_id === job.job_id)
const toggleSaveJob = async (job) => {
  if (!job?.job_id) return
  try {
    if (isJobSaved(job)) {
      await axios.delete(`/api/saved-jobs/${job.job_id}`)
      savedJobs.value = savedJobs.value.filter(j => j.job_id !== job.job_id)
    } else {
      const response = await axios.post('/api/saved-jobs', { job_id: job.job_id })
      if (response?.data?.success) {
        const payload = response.data.data || {
          job_id: job.job_id,
          title: job.title,
          company_name: job.company_name
        }
        savedJobs.value = [
          payload,
          ...savedJobs.value.filter(j => j.job_id !== job.job_id)
        ]
      }
    }
  } catch (error) {
    console.error('Error toggling saved job:', error)
  }
}

const selectJob = (jobOrMatch) => {
  selectedJob.value = jobOrMatch?.job ? jobOrMatch.job : jobOrMatch
  selectedMatch.value = jobOrMatch?.job ? jobOrMatch : null
  showJobModal.value = true
}

const formatTime = (date) => {
  if (!date) return 'Recently'
  const diff = Date.now() - new Date(date).getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  loadCareerMatches()
  loadSavedJobs()
})
</script>
