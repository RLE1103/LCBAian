<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Alumni Directory</h1>
        <p class="text-gray-600 mt-1">Connect with fellow LCBA alumni worldwide</p>
      </div>
      <div class="flex items-center space-x-4">
        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
          Export Directory
        </button>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="flex-1">
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search by name, batch, program, skills, or company..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-2">
          <select v-model="selectedProgram" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Programs</option>
            <option value="BSCS">BSCS</option>
            <option value="BSCpE">BSCpE</option>
            <option value="BSIT">BSIT</option>
            <option value="BSEMC">BSEMC</option>
          </select>

          <select v-model="selectedBatch" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Batches</option>
            <option value="2018">2018</option>
            <option value="2019">2019</option>
            <option value="2020">2020</option>
            <option value="2021">2021</option>
            <option value="2022">2022</option>
            <option value="2023">2023</option>
          </select>

          <select v-model="selectedCompany" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Companies</option>
            <option value="Google">Google</option>
            <option value="Microsoft">Microsoft</option>
            <option value="Amazon">Amazon</option>
            <option value="Meta">Meta</option>
            <option value="Apple">Apple</option>
          </select>

          <select v-model="selectedLocation" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Locations</option>
            <option value="Manila">Manila</option>
            <option value="Cebu">Cebu</option>
            <option value="Davao">Davao</option>
            <option value="Remote">Remote</option>
            <option value="International">International</option>
          </select>

          <select v-model="selectedAvailability" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Availability</option>
            <option value="mentor">Open to Mentor</option>
            <option value="hiring">Hiring</option>
            <option value="work">Open to Work</option>
          </select>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - Alumni Cards -->
      <div class="lg:col-span-2">
        <!-- View Toggle -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-4">
            <button 
              @click="viewMode = 'grid'"
              :class="[
                'px-3 py-1 rounded-lg text-sm font-medium',
                viewMode === 'grid' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-800'
              ]"
            >
              Grid View
            </button>
            <button 
              @click="viewMode = 'list'"
              :class="[
                'px-3 py-1 rounded-lg text-sm font-medium',
                viewMode === 'list' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-800'
              ]"
            >
              List View
            </button>
          </div>
          <div class="text-sm text-gray-600">
            {{ filteredAlumni.length }} alumni found
            </div>
          </div>

        <!-- Alumni Cards -->
        <div v-if="filteredAlumni.length === 0" class="text-center py-12 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <p>No alumni found matching your criteria</p>
          <p class="text-sm">Try adjusting your search or filters</p>
                </div>

        <!-- Grid View -->
        <div v-else-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="alumni in filteredAlumni" 
            :key="alumni.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
            @click="viewProfile(alumni)"
          >
            <div class="flex items-center mb-4">
              <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4">
                {{ alumni.first_name[0] }}{{ alumni.last_name[0] }}
              </div>
                <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ alumni.full_name }}</h3>
                <p class="text-sm text-gray-600">{{ alumni.program }} {{ alumni.batch }}</p>
                <p class="text-sm text-gray-500">{{ alumni.headline || 'Alumni' }}</p>
                </div>
              </div>

            <div class="mb-4">
              <div class="flex flex-wrap gap-2 mb-2">
                <span 
                  v-for="skill in alumni.skills.slice(0, 3)" 
                  :key="skill"
                  class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                >
                  {{ skill }}
                </span>
                <span v-if="alumni.skills.length > 3" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                  +{{ alumni.skills.length - 3 }} more
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span v-if="alumni.is_verified" class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">🏅 Verified</span>
                <span v-if="alumni.is_mentor" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">🎓 Mentor</span>
                <span v-if="alumni.is_hiring" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">📢 Hiring</span>
                <span v-if="alumni.is_open_to_work" class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">💼 Open to Work</span>
              </div>
            </div>

            <div class="flex space-x-2">
              <button 
                @click.stop="sendMessage(alumni)"
                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
              >
                Message
              </button>
              <button 
                @click.stop="viewProfile(alumni)"
                class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50"
              >
                View Profile
              </button>
            </div>
                </div>
              </div>

        <!-- List View -->
        <div v-else class="space-y-4">
          <div 
            v-for="alumni in filteredAlumni" 
            :key="alumni.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
            @click="viewProfile(alumni)"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                  {{ alumni.first_name[0] }}{{ alumni.last_name[0] }}
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">{{ alumni.full_name }}</h3>
                  <p class="text-sm text-gray-600">{{ alumni.program }} {{ alumni.batch }} • {{ alumni.headline || 'Alumni' }}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span 
                      v-for="skill in alumni.skills.slice(0, 3)" 
                      :key="skill"
                      class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                    >
                      {{ skill }}
                    </span>
                  </div>
              </div>
            </div>

              <div class="flex items-center space-x-4">
                <div class="flex items-center gap-2">
                  <span v-if="alumni.is_verified" class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">🏅 Verified</span>
                  <span v-if="alumni.is_mentor" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">🎓 Mentor</span>
                  <span v-if="alumni.is_hiring" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">📢 Hiring</span>
                </div>
                
                <div class="flex space-x-2">
                  <button 
                    @click.stop="sendMessage(alumni)"
                    class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
                  >
                    Message
                  </button>
                  <button 
                    @click.stop="viewProfile(alumni)"
                    class="border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50"
                  >
                    View Profile
                  </button>
                </div>
              </div>
            </div>
                </div>
              </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
          <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Load More Alumni
          </button>
              </div>
            </div>

      <!-- Right Column - Suggested Connections -->
      <div class="space-y-6">
        <!-- Suggested Connections -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Suggested Connections</h3>
          <div class="space-y-4">
            <div 
              v-for="suggestion in suggestedConnections" 
              :key="suggestion.id"
              class="flex items-center justify-between"
            >
              <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                  {{ suggestion.first_name[0] }}{{ suggestion.last_name[0] }}
                </div>
                <div>
                  <h4 class="font-medium text-gray-900">{{ suggestion.full_name }}</h4>
                  <p class="text-sm text-gray-600">{{ suggestion.program }} {{ suggestion.batch }} • {{ suggestion.headline }}</p>
                  <p class="text-xs text-gray-500">{{ suggestion.reason }}</p>
                </div>
              </div>
              <div class="flex space-x-1">
                <button 
                  @click="connectWithAlumni(suggestion)"
                  class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                >
                  Connect
                </button>
                <button 
                  @click="sendMessage(suggestion)"
                  class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                >
                  Message
                </button>
              </div>
            </div>
                </div>
              </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
          <div class="space-y-2">
            <button 
              @click="filterByAvailability('mentor')"
              class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center"
            >
              <span class="mr-2">🎓</span>
              Find Mentors
            </button>
            <button 
              @click="filterByAvailability('hiring')"
              class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center"
            >
              <span class="mr-2">📢</span>
              View Hiring Alumni
            </button>
            <button 
              @click="filterByAvailability('work')"
              class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center"
            >
              <span class="mr-2">💼</span>
              Open to Work
            </button>
            <button 
              @click="viewBatchAlumni"
              class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center"
            >
              <span class="mr-2">👥</span>
              Same Batch Alumni
            </button>
            <button 
              @click="viewCompanyNetworks"
              class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center"
            >
              <span class="mr-2">🏢</span>
              Company Networks
            </button>
              </div>
            </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
          <div class="space-y-3">
            <div class="text-sm">
              <p class="text-gray-700">John Doe joined the platform</p>
              <p class="text-gray-500 text-xs">2 hours ago</p>
            </div>
            <div class="text-sm">
              <p class="text-gray-700">Jane Smith posted a job opening</p>
              <p class="text-gray-500 text-xs">1 day ago</p>
            </div>
            <div class="text-sm">
              <p class="text-gray-700">Mike Johnson became a mentor</p>
              <p class="text-gray-500 text-xs">3 days ago</p>
            </div>
                </div>
              </div>
              </div>
            </div>

    <!-- Profile Modal -->
    <div v-if="showProfileModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto border-4 border-gray-300 shadow-2xl">
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-2xl mr-4">
                {{ selectedAlumni.first_name[0] }}{{ selectedAlumni.last_name[0] }}
              </div>
                <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ selectedAlumni.full_name }}</h2>
                <p class="text-gray-600">{{ selectedAlumni.program }} {{ selectedAlumni.batch }}</p>
                <p class="text-gray-500">{{ selectedAlumni.headline || 'Alumni' }}</p>
                </div>
              </div>
            <button @click="showProfileModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Profile Tabs -->
          <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8">
              <button 
                v-for="tab in profileTabs" 
                :key="tab.key"
                @click="activeProfileTab = tab.key"
                :class="[
                  'py-2 px-1 border-b-2 font-medium text-sm',
                  activeProfileTab === tab.key 
                    ? 'border-blue-500 text-blue-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700'
                ]"
              >
                {{ tab.label }}
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="mb-6">
            <!-- About Tab -->
            <div v-if="activeProfileTab === 'about'">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">About</h3>
                  <p class="text-gray-700 mb-4">{{ selectedAlumni.bio || 'No bio available' }}</p>
                  
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Education</h3>
                  <div class="space-y-3">
                    <div class="border border-gray-200 rounded-lg p-4">
                      <h4 class="font-medium text-gray-900">LCBA (Laguna College of Business and Arts)</h4>
                      <p class="text-sm text-gray-600">{{ selectedAlumni.program }} • {{ selectedAlumni.batch }}</p>
              </div>
            </div>
          </div>

                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Skills</h3>
                  <div class="flex flex-wrap gap-2 mb-6">
                    <span 
                      v-for="skill in selectedAlumni.skills" 
                      :key="skill"
                      class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full"
                    >
                      {{ skill }}
                    </span>
                  </div>
                  
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Badges</h3>
                  <div class="flex flex-wrap gap-2">
                    <span v-if="selectedAlumni.is_verified" class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">🏅 Verified Alumni</span>
                    <span v-if="selectedAlumni.is_mentor" class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">🎓 Mentor</span>
                    <span v-if="selectedAlumni.is_hiring" class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">📢 Hiring</span>
                    <span v-if="selectedAlumni.is_open_to_work" class="bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">💼 Open to Work</span>
                  </div>
          </div>
        </div>
      </div>

            <!-- Experience Tab -->
            <div v-if="activeProfileTab === 'experience'">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Work Experience</h3>
              <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                  <h4 class="font-medium text-gray-900">{{ selectedAlumni.headline || 'Current Position' }}</h4>
                  <p class="text-sm text-gray-600">{{ selectedAlumni.company || 'Company Name' }}</p>
                  <p class="text-sm text-gray-500">2020 - Present</p>
                  <p class="text-gray-700 mt-2">Description of current role and responsibilities...</p>
                </div>
                </div>
              </div>

            <!-- Posts Tab -->
            <div v-if="activeProfileTab === 'posts'">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Posts</h3>
              <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                  <p class="text-gray-700">Shared a job posting at Tech Corp</p>
                  <p class="text-sm text-gray-500">2 days ago</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                  <p class="text-gray-700">Attended the Alumni Meetup 2024</p>
                  <p class="text-sm text-gray-500">1 week ago</p>
              </div>
            </div>
                </div>
              </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4">
            <button @click="showProfileModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
              Close
            </button>
            <button @click="sendMessage(selectedAlumni)" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Send Message
            </button>
            <button @click="requestMentorship(selectedAlumni)" class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">
              Request Mentorship
            </button>
              </div>
            </div>
          </div>
        </div>
      </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../config/api'

// Reactive data
const searchQuery = ref('')
const selectedProgram = ref('')
const selectedBatch = ref('')
const selectedCompany = ref('')
const selectedLocation = ref('')
const selectedAvailability = ref('')
const viewMode = ref('grid')
const showProfileModal = ref(false)
const selectedAlumni = ref({})
const activeProfileTab = ref('about')
const loading = ref(false)

// Fetch alumni from API
const alumni = ref([])

const fetchAlumni = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value || undefined,
      program: selectedProgram.value || undefined,
      batch: selectedBatch.value || undefined,
      role: 'alumni',
    }
    
    const response = await axios.get('/api/users', { params })
    if (response.data.success) {
      // Format the data for the view
      alumni.value = response.data.data.map(user => ({
        id: user.id,
        first_name: user.first_name,
        last_name: user.last_name,
        full_name: `${user.first_name} ${user.last_name}`,
        program: user.program,
        batch: user.batch,
        headline: user.headline,
        company: user.current_job_title,
        bio: user.bio,
        skills: user.skills || [],
        is_verified: true,
        is_mentor: user.role === 'mentor',
        is_hiring: false,
        is_open_to_work: !user.current_job_title
      }))
    }
  } catch (error) {
    console.error('Error fetching alumni:', error)
    alumni.value = []
  } finally {
    loading.value = false
  }
}

const suggestedConnections = ref([
  {
    id: 6,
    first_name: 'Alex',
    last_name: 'Chen',
    full_name: 'Alex Chen',
    program: 'BSCS',
    batch: '2020',
    headline: 'Software Engineer at Google',
    reason: 'Same batch and program'
  },
  {
    id: 7,
    first_name: 'Sarah',
    last_name: 'Martinez',
    full_name: 'Sarah Martinez',
    program: 'BSCpE',
    batch: '2019',
    headline: 'Senior Developer at Microsoft',
    reason: 'Similar skills and industry'
  },
  {
    id: 8,
    first_name: 'Ryan',
    last_name: 'Kim',
    full_name: 'Ryan Kim',
    program: 'BSIT',
    batch: '2021',
    headline: 'Product Manager at Amazon',
    reason: 'Same company network'
  }
])

const profileTabs = [
  { key: 'about', label: 'About' },
  { key: 'experience', label: 'Experience' },
  { key: 'posts', label: 'Posts & Activity' }
]

// Computed properties
const filteredAlumni = computed(() => {
  let filtered = alumni.value

  // Apply search filter
  if (searchQuery.value) {
    filtered = filtered.filter(alumni => 
      alumni.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      alumni.program.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      alumni.batch.includes(searchQuery.value) ||
      alumni.headline.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      alumni.skills.some(skill => skill.toLowerCase().includes(searchQuery.value.toLowerCase()))
    )
  }

  // Apply program filter
  if (selectedProgram.value) {
    filtered = filtered.filter(alumni => alumni.program === selectedProgram.value)
  }

  // Apply batch filter
  if (selectedBatch.value) {
    filtered = filtered.filter(alumni => alumni.batch === selectedBatch.value)
  }

  // Apply company filter
  if (selectedCompany.value) {
    filtered = filtered.filter(alumni => alumni.company === selectedCompany.value)
  }

  // Apply location filter
  if (selectedLocation.value) {
    filtered = filtered.filter(alumni => alumni.location.includes(selectedLocation.value))
  }

  // Apply availability filter
  if (selectedAvailability.value) {
    switch (selectedAvailability.value) {
      case 'mentor':
        filtered = filtered.filter(alumni => alumni.is_mentor)
        break
      case 'hiring':
        filtered = filtered.filter(alumni => alumni.is_hiring)
        break
      case 'work':
        filtered = filtered.filter(alumni => alumni.is_open_to_work)
        break
    }
  }

  return filtered
})

// Methods
const viewProfile = (alumni) => {
  selectedAlumni.value = alumni
  showProfileModal.value = true
  activeProfileTab.value = 'about'
}

const sendMessage = (alumni) => {
  // Navigate to messages with this alumni
  console.log('Send message to:', alumni.full_name)
}

const connectWithAlumni = (alumni) => {
  console.log('Connect with:', alumni.full_name)
}

const requestMentorship = (alumni) => {
  console.log('Request mentorship from:', alumni.full_name)
}

const filterByAvailability = (type) => {
  selectedAvailability.value = type
}

const viewBatchAlumni = () => {
  // Show alumni from same batch
  console.log('View batch alumni')
}

const viewCompanyNetworks = () => {
  // Show alumni by company
  console.log('View company networks')
}

onMounted(() => {
  fetchAlumni()
})

// Watch for filter changes and refetch
watch([searchQuery, selectedProgram, selectedBatch], () => {
  fetchAlumni()
})
</script>