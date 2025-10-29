<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Mentorship Hub</h1>
        <p class="text-gray-600 mt-1">Connect with fellow alumni mentors and grow your career</p>
          </div>
        </div>
        
    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-md mb-6">
      <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6">
          <button 
            v-for="tab in tabs" 
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm',
              activeTab === tab.key 
                ? 'border-blue-500 text-blue-600' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            {{ tab.label }}
            <span v-if="tab.count" class="ml-2 bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs">
              {{ tab.count }}
            </span>
          </button>
        </nav>
      </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow-md">
      <!-- Find a Mentor Tab -->
      <div v-if="activeTab === 'find'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Find a Mentor</h2>
    </div>

        <!-- Search and Filters -->
        <div class="flex items-center gap-4 mb-6">
          <div class="flex-1">
          <input
              v-model="mentorSearch"
            type="text"
            placeholder="Search by name, expertise, industry, batch, or availability..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
          <select v-model="selectedExpertise" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Expertise</option>
            <option value="software-engineering">Software Engineering</option>
            <option value="product-management">Product Management</option>
            <option value="data-science">Data Science</option>
            <option value="ui-ux">UI/UX Design</option>
            <option value="business">Business</option>
            <option value="marketing">Marketing</option>
          </select>
          <select v-model="selectedIndustry" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Industries</option>
            <option value="technology">Technology</option>
            <option value="finance">Finance</option>
            <option value="healthcare">Healthcare</option>
            <option value="education">Education</option>
            <option value="consulting">Consulting</option>
          </select>
          <select v-model="selectedBatch" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Batches</option>
            <option value="2018">2018</option>
            <option value="2019">2019</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
          </select>
      </div>

        <!-- Mentor Cards -->
        <div v-if="filteredMentors.length === 0" class="text-center py-12 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <p>No mentors found</p>
          <p class="text-sm">Try adjusting your search or filters</p>
    </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="mentor in filteredMentors" 
            :key="mentor.id"
            class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow"
          >
        <div class="flex items-start gap-4 mb-4">
              <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                {{ mentor.first_name[0] }}{{ mentor.last_name[0] }}
          </div>
          <div class="flex-1">
            <div class="flex items-start justify-between">
              <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ mentor.full_name }}</h3>
                    <p class="text-sm text-gray-600">{{ mentor.headline }}</p>
              </div>
                  <span class="text-sm text-gray-500">Batch {{ mentor.batch }}</span>
            </div>
          </div>
        </div>
        
        <div class="space-y-3 mb-6">
          <div>
                <span class="text-sm font-medium text-gray-700">Skills:</span>
                <div class="flex flex-wrap gap-2 mt-1">
                  <span 
                    v-for="skill in mentor.skills.slice(0, 3)" 
                    :key="skill"
                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                  >
                    {{ skill }}
                  </span>
                  <span v-if="mentor.skills.length > 3" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                    +{{ mentor.skills.length - 3 }} more
                  </span>
            </div>
          </div>
          <div>
                <span class="text-sm font-medium text-gray-700">Availability:</span>
            <div class="mt-1">
                  <span :class="[
                    'inline-block px-2 py-1 text-xs rounded-full',
                    mentor.availability === 'available' ? 'bg-green-100 text-green-800' :
                    mentor.availability === 'busy' ? 'bg-yellow-100 text-yellow-800' :
                    'bg-red-100 text-red-800'
                  ]">
                    {{ getAvailabilityLabel(mentor.availability, mentor.next_available) }}
                  </span>
                </div>
              </div>
              <div v-if="mentor.rating">
                <span class="text-sm font-medium text-gray-700">Rating:</span>
                <div class="flex items-center mt-1">
                  <div class="flex text-yellow-400">
                    <span v-for="i in 5" :key="i" class="text-sm">
                      {{ i <= mentor.rating ? '★' : '☆' }}
                    </span>
                  </div>
                  <span class="text-sm text-gray-600 ml-2">({{ mentor.reviews_count }} reviews)</span>
            </div>
          </div>
        </div>
        
        <div class="flex gap-3">
              <button 
                @click="viewMentorProfile(mentor)"
                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
              >
            View Profile
          </button>
              <button 
                @click="requestMentorship(mentor)"
                class="flex-1 border border-blue-600 text-blue-600 py-2 px-4 rounded-lg hover:bg-blue-50"
              >
            Request Mentorship
          </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Offer Mentorship Tab -->
      <div v-if="activeTab === 'offer'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Offer Mentorship</h2>
          <button 
            @click="showOfferModal = true"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
          >
            Update Profile
          </button>
          </div>
        <div class="mb-4">
          <button @click="activeTab = 'find'" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to Find a Mentor</button>
        </div>

        <!-- Mentorship Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                <p class="text-2xl font-semibold text-gray-900">{{ pendingRequests }}</p>
            </div>
          </div>
        </div>
        
          <div class="bg-green-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Average Rating</p>
                <p class="text-2xl font-semibold text-gray-900">{{ averageRating }}/5</p>
        </div>
            </div>
          </div>

          <div class="bg-purple-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Mentees</p>
                <p class="text-2xl font-semibold text-gray-900">{{ totalMentees }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Mentorship Settings -->
        <div class="bg-gray-50 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Mentorship Settings</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Areas of Expertise</label>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="expertise in currentExpertise" 
                  :key="expertise"
                  class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full flex items-center"
                >
                  {{ expertise }}
                  <button @click="removeExpertise(expertise)" class="ml-2 text-blue-600 hover:text-blue-800">×</button>
                </span>
              </div>
              <div class="flex mt-2">
                <input 
                  v-model="newExpertise"
                  @keypress.enter="addExpertise"
                  type="text"
                  placeholder="Add expertise..."
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <button @click="addExpertise" class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                  Add
          </button>
        </div>
      </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Availability Schedule</label>
              <div class="space-y-2">
                <div class="flex items-center">
                  <input type="checkbox" v-model="availabilitySettings.monday" class="mr-2">
                  <span class="text-sm text-gray-700">Monday</span>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" v-model="availabilitySettings.tuesday" class="mr-2">
                  <span class="text-sm text-gray-700">Tuesday</span>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" v-model="availabilitySettings.wednesday" class="mr-2">
                  <span class="text-sm text-gray-700">Wednesday</span>
                </div>
                <div class="flex items-center">
                  <input type="checkbox" v-model="availabilitySettings.thursday" class="mr-2">
                  <span class="text-sm text-gray-700">Thursday</span>
          </div>
                <div class="flex items-center">
                  <input type="checkbox" v-model="availabilitySettings.friday" class="mr-2">
                  <span class="text-sm text-gray-700">Friday</span>
              </div>
            </div>
          </div>
        </div>
        
          <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Mentoring Style</label>
            <div class="flex space-x-4">
              <label class="flex items-center">
                <input type="radio" v-model="mentoringStyle" value="1-on-1" class="mr-2">
                <span class="text-sm text-gray-700">1-on-1</span>
              </label>
              <label class="flex items-center">
                <input type="radio" v-model="mentoringStyle" value="group" class="mr-2">
                <span class="text-sm text-gray-700">Group</span>
              </label>
              <label class="flex items-center">
                <input type="radio" v-model="mentoringStyle" value="virtual" class="mr-2">
                <span class="text-sm text-gray-700">Virtual</span>
              </label>
              <label class="flex items-center">
                <input type="radio" v-model="mentoringStyle" value="in-person" class="mr-2">
                <span class="text-sm text-gray-700">In-person</span>
              </label>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end">
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Save Settings
            </button>
            </div>
          </div>
        </div>
        
      <!-- My Sessions Tab -->
      <div v-if="activeTab === 'sessions'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">My Sessions</h2>
          <div class="flex items-center space-x-4">
            <select v-model="sessionFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="">All Sessions</option>
              <option value="upcoming">Upcoming</option>
              <option value="past">Past</option>
              <option value="pending">Pending</option>
            </select>
        </div>
      </div>
        <div class="mb-4">
          <button @click="activeTab = 'find'" class="text-blue-600 hover:text-blue-800 text-sm font-medium">← Back to Find a Mentor</button>
        </div>

        <!-- Sessions Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mentor/Mentee</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="session in filteredSessions" 
                :key="session.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ session.preferred_slot ? formatDate(session.preferred_slot) : 'Not set' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ session.preferred_slot ? formatTime(session.preferred_slot) : 'Not set' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs mr-2">
                      {{ session.mentor.first_name[0] }}{{ session.mentor.last_name[0] }}
          </div>
              <div>
                      <div class="text-sm font-medium text-gray-900">{{ session.mentor.first_name }} {{ session.mentor.last_name }}</div>
                      <div class="text-sm text-gray-500">{{ session.mentee.first_name }} {{ session.mentee.last_name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ session.goal || 'General mentorship' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    session.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    session.status === 'scheduled' ? 'bg-green-100 text-green-800' :
                    session.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                    'bg-gray-100 text-gray-800'
                  ]">
                    {{ session.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button 
                      @click="viewSession(session)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View
                    </button>
                    <button 
                      v-if="session.status === 'scheduled'"
                      @click="rescheduleSession(session)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Reschedule
          </button>
                    <button 
                      v-if="session.status === 'completed'"
                      @click="giveFeedback(session)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Feedback
          </button>
              </div>
                </td>
              </tr>
            </tbody>
          </table>
            </div>
          </div>
        </div>
        
    <!-- Request Mentorship Modal -->
    <div v-if="showRequestModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 w-full max-w-md border-4 border-gray-300 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Request Mentorship</h3>
          <button @click="showRequestModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
            </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Mentor</label>
          <div class="flex items-center p-3 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
              {{ selectedMentor.first_name[0] }}{{ selectedMentor.last_name[0] }}
          </div>
          <div>
              <div class="text-sm font-medium text-gray-900">{{ selectedMentor.full_name }}</div>
              <div class="text-sm text-gray-600">{{ selectedMentor.headline }}</div>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Goal</label>
          <select v-model="mentorshipGoal" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select a goal...</option>
            <option value="career-guidance">Career Guidance</option>
            <option value="resume-review">Resume Review</option>
            <option value="mock-interview">Mock Interview</option>
            <option value="skill-development">Skill Development</option>
            <option value="networking">Networking</option>
            <option value="other">Other</option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time Slot</label>
          <input 
            v-model="preferredSlot"
            type="datetime-local"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Additional Message (Optional)</label>
          <textarea 
            v-model="additionalMessage"
            rows="3"
            placeholder="Tell the mentor about your specific needs..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>
        
        <div class="flex justify-end space-x-2">
          <button @click="showRequestModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="submitMentorshipRequest" :disabled="!mentorshipGoal" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            Send Request
          </button>
        </div>
      </div>
    </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../config/api'

// Reactive data
const activeTab = ref('find')
const mentorSearch = ref('')
const selectedExpertise = ref('')
const selectedIndustry = ref('')
const selectedBatch = ref('')
const sessionFilter = ref('')
const showRequestModal = ref(false)
const showOfferModal = ref(false)
const selectedMentor = ref({})
const mentorshipGoal = ref('')
const preferredSlot = ref('')
const additionalMessage = ref('')
const newExpertise = ref('')
const mentoringStyle = ref('1-on-1')
const loading = ref(false)

const currentExpertise = ref(['Software Engineering', 'Product Management', 'Data Science'])
const availabilitySettings = ref({
  monday: true,
  tuesday: true,
  wednesday: false,
  thursday: true,
  friday: false
})

// Fetch mentors from API
const mentors = ref([])

const fetchMentors = async () => {
  loading.value = true
  try {
    const params = {
      search: mentorSearch.value || undefined,
      role: 'alumni'
    }
    
    const response = await axios.get('/api/users', { params })
    if (response?.data?.success && Array.isArray(response.data.data)) {
      mentors.value = response.data.data.map(user => ({
        id: user.id,
        first_name: user.first_name,
        last_name: user.last_name,
        full_name: `${user.first_name} ${user.last_name}`,
        headline: user.headline || 'Alumni Mentor',
        batch: user.batch,
        skills: user.skills || [],
        availability: 'available',
        next_available: null,
        rating: 4.5,
        reviews_count: 0,
        industry: user.industry || 'technology',
        expertise: 'software-engineering'
      }))
    } else {
      mentors.value = []
    }
  } catch (error) {
    console.error('Error fetching mentors:', error)
    mentors.value = []
  } finally {
    loading.value = false
  }
}

// Fetch mentorship sessions from API
const sessions = ref([])

const fetchSessions = async () => {
  try {
    const response = await axios.get('/api/mentorships')
    if (response?.data?.success && Array.isArray(response.data.data)) {
      sessions.value = response.data.data
    } else {
      sessions.value = []
    }
  } catch (error) {
    console.error('Error fetching sessions:', error)
    sessions.value = []
  }
}

const submitMentorshipRequest = async () => {
  try {
    const response = await axios.post('/api/mentorships', {
      mentor_id: selectedMentor.value.id,
      goal: mentorshipGoal.value,
      preferred_slot: preferredSlot.value,
      additional_message: additionalMessage.value
    })
    
    if (response.data.success) {
      showRequestModal.value = false
      mentorshipGoal.value = ''
      preferredSlot.value = ''
      additionalMessage.value = ''
      selectedMentor.value = {}
      
      // Refresh sessions
      await fetchSessions()
    }
  } catch (error) {
    console.error('Error submitting mentorship request:', error)
  }
}

// Computed properties
const filteredMentors = computed(() => {
  // Since we're filtering on the backend, just return the mentors
  return mentors.value
})

const filteredSessions = computed(() => {
  let filtered = sessions.value

  if (sessionFilter.value) {
    filtered = filtered.filter(session => session.status === sessionFilter.value)
  }

  return filtered
})

const tabs = [
  { key: 'find', label: 'Find a Mentor', count: mentors.value.length },
  { key: 'sessions', label: 'My Sessions', count: sessions.value.length },
  { key: 'offer', label: 'Offer Mentorship', count: null }
]

// Methods
const requestMentorship = (mentor) => {
  selectedMentor.value = mentor
  showRequestModal.value = true
}

const updateMentorshipStatus = async (sessionId, status) => {
  try {
    const response = await axios.put(`/api/mentorships/${sessionId}`, { status })
    if (response.data.success) {
      // Update the session status
      const session = sessions.value.find(s => s.id === sessionId)
      if (session) {
        session.status = status
      }
    }
  } catch (error) {
    console.error('Error updating mentorship status:', error)
  }
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

onMounted(() => {
  fetchMentors()
  fetchSessions()
})

// Watch for search changes and refetch
watch(mentorSearch, () => {
  fetchMentors()
})
</script>