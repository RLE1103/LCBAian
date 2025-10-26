<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Jobs</h1>
      <div class="flex items-center space-x-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          Post a Job
        </button>
        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
          Saved Jobs
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
              placeholder="Search jobs..." 
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
          <select v-model="selectedLocation" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Locations</option>
            <option value="remote">Remote</option>
            <option value="new-york">New York</option>
            <option value="san-francisco">San Francisco</option>
            <option value="chicago">Chicago</option>
          </select>

          <select v-model="selectedWorkType" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Work Types</option>
            <option value="full-time">Full-time</option>
            <option value="part-time">Part-time</option>
            <option value="internship">Internship</option>
            <option value="contract">Contract</option>
          </select>

          <select v-model="selectedIndustry" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Industries</option>
            <option value="technology">Technology</option>
            <option value="finance">Finance</option>
            <option value="healthcare">Healthcare</option>
            <option value="education">Education</option>
          </select>

          <select v-model="selectedExperience" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Experience Levels</option>
            <option value="entry">Entry Level</option>
            <option value="mid">Mid Level</option>
            <option value="senior">Senior Level</option>
            <option value="executive">Executive</option>
          </select>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - Job Listings -->
      <div class="lg:col-span-2">
        <div class="space-y-4">
          <div v-if="filteredJobs.length === 0" class="text-center py-12 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
            </svg>
            <p>No jobs found matching your criteria</p>
            <p class="text-sm">Try adjusting your search or filters</p>
          </div>

          <div 
            v-for="job in filteredJobs" 
            :key="job.id"
            @click="selectJob(job)"
            :class="[
              'bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow border-l-4',
              selectedJob?.id === job.id ? 'border-blue-500' : 'border-transparent'
            ]"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <h3 class="text-lg font-semibold text-gray-900">{{ job.title }}</h3>
                  <span v-if="job.posted_by_admin" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                    Alumni Posted
                  </span>
      </div>
                
                <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                  <span class="font-medium">{{ job.company_name }}</span>
                  <span>{{ job.location }}</span>
                  <span>{{ formatTime(job.created_at) }}</span>
    </div>

                <p class="text-gray-700 text-sm mb-4 line-clamp-2">{{ job.description }}</p>

                <div class="flex items-center justify-between">
                  <div class="flex flex-wrap gap-2">
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                      {{ job.work_type || 'Full-time' }}
                    </span>
                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                      {{ job.industry || 'Technology' }}
                    </span>
                    <span v-if="job.location === 'Remote'" class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                      Remote
                    </span>
                  </div>

                  <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                      </svg>
                    </button>
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                      </svg>
                    </button>
                </div>
              </div>
            </div>
              </div>
            </div>
          </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
          <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Load More Jobs
          </button>
                </div>
              </div>

      <!-- Right Column - Quick Panels -->
      <div class="space-y-6">
        <!-- Saved Jobs -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Saved Jobs</h3>
          <div class="space-y-3">
            <div v-for="savedJob in savedJobs" :key="savedJob.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                <h4 class="font-medium text-sm text-gray-900">{{ savedJob.title }}</h4>
                <p class="text-xs text-gray-600">{{ savedJob.company_name }}</p>
              </div>
              <button class="text-red-500 hover:text-red-700">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
            <div v-if="savedJobs.length === 0" class="text-center text-gray-500 text-sm py-4">
              No saved jobs yet
              </div>
            </div>
          </div>

        <!-- Recommended Jobs -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Recommended for You</h3>
          <div class="space-y-3">
            <div v-for="recommendedJob in recommendedJobs" :key="recommendedJob.id" class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
              <h4 class="font-medium text-sm text-gray-900">{{ recommendedJob.title }}</h4>
              <p class="text-xs text-gray-600">{{ recommendedJob.company_name }} • {{ recommendedJob.location }}</p>
              <div class="mt-2">
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                  {{ recommendedJob.match_score }}% match
                </span>
              </div>
                </div>
              </div>
            </div>

        <!-- Your Job Posts -->
        <div v-if="userJobPosts.length > 0" class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Job Posts</h3>
          <div class="space-y-3">
            <div v-for="jobPost in userJobPosts" :key="jobPost.id" class="p-3 border border-gray-200 rounded-lg">
              <h4 class="font-medium text-sm text-gray-900">{{ jobPost.title }}</h4>
              <p class="text-xs text-gray-600">{{ jobPost.applications_count }} applications</p>
              <div class="mt-2 flex space-x-2">
                <button class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">View</button>
                <button class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Edit</button>
              </div>
            </div>
          </div>
          </div>
        </div>
      </div>

    <!-- Job Detail Modal -->
    <div v-if="showJobModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto border-4 border-gray-300 shadow-2xl">
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
                <div>
              <h2 class="text-2xl font-bold text-gray-900">{{ selectedJob?.title }}</h2>
              <div class="flex items-center gap-4 text-gray-600 mt-2">
                <span class="font-medium">{{ selectedJob?.company_name }}</span>
                <span>{{ selectedJob?.location }}</span>
                <span>{{ formatTime(selectedJob?.created_at) }}</span>
              </div>
            </div>
            <button @click="showJobModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Job Details Tabs -->
          <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8">
              <button 
                v-for="tab in jobDetailTabs" 
                :key="tab.key"
                @click="activeTab = tab.key"
                :class="[
                  'py-2 px-1 border-b-2 font-medium text-sm',
                  activeTab === tab.key 
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
            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'">
              <div class="prose max-w-none">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Description</h3>
                <p class="text-gray-700 mb-6">{{ selectedJob?.description }}</p>
                
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Requirements</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                  <li>Bachelor's degree in Computer Science or related field</li>
                  <li>3+ years of experience in software development</li>
                  <li>Proficiency in JavaScript, React, and Node.js</li>
                  <li>Experience with cloud platforms (AWS, Azure, or GCP)</li>
                  <li>Strong problem-solving and communication skills</li>
                </ul>

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Benefits</h3>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                  <li>Competitive salary and equity package</li>
                  <li>Comprehensive health, dental, and vision insurance</li>
                  <li>Flexible work arrangements and remote options</li>
                  <li>Professional development opportunities</li>
                  <li>Generous vacation and sick leave</li>
                </ul>
                </div>
              </div>

            <!-- About Company Tab -->
            <div v-if="activeTab === 'company'">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">About {{ selectedJob?.company_name }}</h3>
                <p class="text-gray-700 mb-4">
                  {{ selectedJob?.company_name }} is a leading technology company focused on innovation and growth. 
                  We're committed to creating a diverse and inclusive workplace where everyone can thrive.
                </p>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="font-medium text-gray-900">Company Size:</span>
                    <span class="text-gray-700 ml-2">500+ employees</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-900">Industry:</span>
                    <span class="text-gray-700 ml-2">Technology</span>
          </div>
                  <div>
                    <span class="font-medium text-gray-900">Founded:</span>
                    <span class="text-gray-700 ml-2">2015</span>
            </div>
                <div>
                    <span class="font-medium text-gray-900">Location:</span>
                    <span class="text-gray-700 ml-2">{{ selectedJob?.location }}</span>
              </div>
            </div>
          </div>
        </div>

            <!-- Applicants Tab -->
            <div v-if="activeTab === 'applicants'">
              <div class="space-y-4">
                <div v-for="applicant in jobApplicants" :key="applicant.id" class="border border-gray-200 rounded-lg p-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center">
                      <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                        {{ applicant.user.first_name[0] }}{{ applicant.user.last_name[0] }}
                      </div>
                <div>
                        <h4 class="font-medium text-gray-900">{{ applicant.user.full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ applicant.user.headline }}</p>
                </div>
              </div>
                    <div class="flex items-center space-x-2">
                      <span :class="[
                        'px-2 py-1 rounded-full text-xs font-medium',
                        applicant.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        applicant.status === 'reviewed' ? 'bg-blue-100 text-blue-800' :
                        applicant.status === 'accepted' ? 'bg-green-100 text-green-800' :
                        'bg-red-100 text-red-800'
                      ]">
                        {{ applicant.status }}
                      </span>
                      <button class="text-blue-600 hover:text-blue-800 text-sm">Message</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

          <!-- Apply Button -->
          <div class="flex justify-end space-x-4">
            <button @click="showJobModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
              Cancel
            </button>
            <button @click="applyForJob" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Apply Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Apply Modal -->
    <div v-if="showApplyModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md border-4 border-gray-300 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Apply for {{ selectedJob?.title }}</h3>
          <button @click="showApplyModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Select Resume</label>
          <select v-model="selectedResume" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Choose a resume...</option>
            <option v-for="resume in userResumes" :key="resume.id" :value="resume.id">
              {{ resume.title }}
            </option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter (Optional)</label>
          <textarea 
            v-model="coverLetter"
            rows="4"
            placeholder="Write a cover letter..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>
        
        <div class="flex justify-end space-x-2">
          <button @click="showApplyModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="submitApplication" :disabled="!selectedResume" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            Submit Application
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const searchQuery = ref('')
const selectedLocation = ref('')
const selectedWorkType = ref('')
const selectedIndustry = ref('')
const selectedExperience = ref('')
const selectedJob = ref(null)
const showJobModal = ref(false)
const showApplyModal = ref(false)
const activeTab = ref('overview')
const selectedResume = ref('')
const coverLetter = ref('')

// Sample data - replace with API calls
const jobs = ref([
  {
    id: 1,
    title: 'Senior Software Engineer',
    company_name: 'Tech Corp',
    location: 'San Francisco, CA',
    description: 'We are looking for a Senior Software Engineer to join our growing team. You will be responsible for developing and maintaining our core platform.',
    work_type: 'Full-time',
    industry: 'Technology',
    experience_level: 'Senior',
    posted_by_admin: true,
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
    applications_count: 15
  },
  {
    id: 2,
    title: 'Frontend Developer',
    company_name: 'StartupXYZ',
    location: 'Remote',
    description: 'Join our innovative startup as a Frontend Developer. Work with React, TypeScript, and modern web technologies.',
    work_type: 'Full-time',
    industry: 'Technology',
    experience_level: 'Mid',
    posted_by_admin: false,
    created_at: new Date(Date.now() - 5 * 60 * 60 * 1000), // 5 hours ago
    applications_count: 8
  },
  {
    id: 3,
    title: 'Data Scientist',
    company_name: 'Data Inc',
    location: 'New York, NY',
    description: 'We are seeking a Data Scientist to help us extract insights from large datasets and build machine learning models.',
    work_type: 'Full-time',
    industry: 'Technology',
    experience_level: 'Mid',
    posted_by_admin: true,
    created_at: new Date(Date.now() - 24 * 60 * 60 * 1000), // 1 day ago
    applications_count: 12
  },
  {
    id: 4,
    title: 'Product Manager',
    company_name: 'InnovateCo',
    location: 'Chicago, IL',
    description: 'Lead product development initiatives and work closely with engineering and design teams to deliver exceptional user experiences.',
    work_type: 'Full-time',
    industry: 'Technology',
    experience_level: 'Senior',
    posted_by_admin: false,
    created_at: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000), // 3 days ago
    applications_count: 20
  }
])

const savedJobs = ref([
  { id: 1, title: 'Senior Software Engineer', company_name: 'Tech Corp' },
  { id: 2, title: 'Frontend Developer', company_name: 'StartupXYZ' }
])

const recommendedJobs = ref([
  { id: 5, title: 'Full Stack Developer', company_name: 'WebCorp', location: 'Remote', match_score: 95 },
  { id: 6, title: 'React Developer', company_name: 'AppStudio', location: 'Austin, TX', match_score: 88 },
  { id: 7, title: 'Software Engineer', company_name: 'CloudTech', location: 'Seattle, WA', match_score: 82 }
])

const userJobPosts = ref([
  { id: 8, title: 'Junior Developer', applications_count: 5 },
  { id: 9, title: 'UI/UX Designer', applications_count: 12 }
])

const jobApplicants = ref([
  {
    id: 1,
    user: { first_name: 'John', last_name: 'Doe', full_name: 'John Doe', headline: 'Software Engineer' },
    status: 'pending'
  },
  {
    id: 2,
    user: { first_name: 'Jane', last_name: 'Smith', full_name: 'Jane Smith', headline: 'Frontend Developer' },
    status: 'reviewed'
  }
])

const userResumes = ref([
  { id: 1, title: 'Software Engineer Resume' },
  { id: 2, title: 'Frontend Developer Resume' },
  { id: 3, title: 'Full Stack Developer Resume' }
])

const jobDetailTabs = [
  { key: 'overview', label: 'Overview' },
  { key: 'company', label: 'About the Company' },
  { key: 'applicants', label: 'Applicants' }
]

// Computed properties
const filteredJobs = computed(() => {
  let filtered = jobs.value

  // Apply search filter
  if (searchQuery.value) {
    filtered = filtered.filter(job => 
      job.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      job.company_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      job.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Apply location filter
  if (selectedLocation.value) {
    filtered = filtered.filter(job => 
      job.location.toLowerCase().includes(selectedLocation.value.toLowerCase())
    )
  }

  // Apply work type filter
  if (selectedWorkType.value) {
    filtered = filtered.filter(job => 
      job.work_type.toLowerCase() === selectedWorkType.value.toLowerCase()
    )
  }

  // Apply industry filter
  if (selectedIndustry.value) {
    filtered = filtered.filter(job => 
      job.industry.toLowerCase() === selectedIndustry.value.toLowerCase()
    )
  }

  // Apply experience filter
  if (selectedExperience.value) {
    filtered = filtered.filter(job => 
      job.experience_level.toLowerCase() === selectedExperience.value.toLowerCase()
    )
  }

  return filtered
})

// Methods
const selectJob = (job) => {
  selectedJob.value = job
  showJobModal.value = true
  activeTab.value = 'overview'
}

const applyForJob = () => {
  showJobModal.value = false
  showApplyModal.value = true
}

const submitApplication = () => {
  if (!selectedResume.value) return

  // Submit application logic here
  console.log('Submitting application:', {
    jobId: selectedJob.value.id,
    resumeId: selectedResume.value,
    coverLetter: coverLetter.value
  })

  showApplyModal.value = false
  selectedResume.value = ''
  coverLetter.value = ''
}

const formatTime = (date) => {
  if (!date) return ''
  
  const now = new Date()
  const diff = now - new Date(date)
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  // Load initial data
})
</script>

<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}
</style>