<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Jobs</h1>
    <div class="flex items-center space-x-4">
      <button @click="showCreateJobModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        Post a Job
      </button>
        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
          Bookmarked Jobs
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
            :key="job.job_id"
            @click="selectJob(job)"
            :class="[
              'bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow border-l-4',
              selectedJob?.job_id === job.job_id ? 'border-blue-500' : 'border-transparent'
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
                    <button @click.stop="toggleSaveJob(job)" :title="isJobSaved(job) ? 'Unsave' : 'Save'" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
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
        <!-- Career Matching -->
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Career Matching</h3>
              <p class="text-xs text-gray-600 mt-1">Personalized job recommendations based on your profile</p>
            </div>
            <div class="flex items-center space-x-2">
              <button 
                @click="loadCareerMatching"
                :disabled="matchingLoading"
                class="text-gray-500 hover:text-gray-700"
                title="Refresh recommendations"
              >
                <svg v-if="matchingLoading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="matchingLoading" class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600 text-sm">Finding your perfect matches...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="careerMatches.length === 0" class="text-center py-8 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <p class="font-medium text-sm">No matches found</p>
            <p class="text-xs mt-1">Add more skills to your profile for better matches!</p>
            <button @click="loadCareerMatching" class="mt-4 text-blue-600 hover:text-blue-800 text-sm font-medium">
              Try Again
            </button>
          </div>

          <!-- Career Matches List -->
          <div v-else class="space-y-4">
            <div 
              v-for="match in careerMatches" 
              :key="match.job?.job_id || match.job_id"
              class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer"
              @click="selectJob(match.job || match)"
            >
              <!-- Job Header -->
              <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                  <h4 class="font-semibold text-gray-900 text-sm">{{ match.job?.title || match.title }}</h4>
                  <p class="text-sm text-gray-600">{{ match.job?.company_name || match.company_name }}</p>
                  <div class="flex items-center gap-2 mt-1">
                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                      {{ match.job?.location || match.location }}
                    </span>
                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                      {{ match.job?.work_type || match.work_type }}
                    </span>
                  </div>
                </div>
                
                <!-- Match Score -->
                <div class="text-right">
                  <div class="flex items-center gap-2">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                      {{ getMatchPercentage(match) }}%
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">Match Score</p>
                </div>
              </div>

              <!-- Match Summary -->
              <div v-if="match.match_summary" class="mb-3">
                <p class="text-sm text-gray-700">{{ match.match_summary }}</p>
              </div>

              <!-- Skills Match -->
              <div class="space-y-2">
                <!-- Matched Skills -->
                <div v-if="match.matched_skills && match.matched_skills.length > 0">
                  <p class="text-xs font-medium text-green-700 mb-1">✓ Matched Skills:</p>
                  <div class="flex flex-wrap gap-1">
                    <span 
                      v-for="skill in match.matched_skills.slice(0, 3)" 
                      :key="skill"
                      class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full"
                    >
                      {{ skill }}
                    </span>
                    <span v-if="match.matched_skills.length > 3" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                      +{{ match.matched_skills.length - 3 }} more
                    </span>
                  </div>
                </div>

                <!-- Missing Skills -->
                <div v-if="match.missing_skills && match.missing_skills.length > 0">
                  <p class="text-xs font-medium text-orange-700 mb-1">⚠ Missing Skills:</p>
                  <div class="flex flex-wrap gap-1">
                    <span 
                      v-for="skill in match.missing_skills.slice(0, 2)" 
                      :key="skill"
                      class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full"
                    >
                      {{ skill }}
                    </span>
                    <span v-if="match.missing_skills.length > 2" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                      +{{ match.missing_skills.length - 2 }} more
                    </span>
                  </div>
                </div>

                <!-- Bonus Skills -->
                <div v-if="match.bonus_skills && match.bonus_skills.length > 0">
                  <p class="text-xs font-medium text-blue-700 mb-1">⭐ Bonus Skills:</p>
                  <div class="flex flex-wrap gap-1">
                    <span 
                      v-for="skill in match.bonus_skills.slice(0, 2)" 
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
                  @click.stop="selectJob(match.job || match)"
                  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium"
                >
                  View Details
                </button>
                <button 
                  @click.stop="toggleSaveJob(match.job || match)"
                  :title="isJobSaved(match.job || match) ? 'Unsave' : 'Save'"
                  class="text-gray-600 hover:text-gray-800 text-sm font-medium"
                >
                  {{ isJobSaved(match.job || match) ? 'Saved' : 'Save' }}
                </button>
              </div>
            </div>
          </div>

          <!-- View More Button -->
          <div v-if="careerMatches.length > 0" class="mt-6 text-center">
            <button 
              @click="viewAllCareerMatches"
              class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            >
              View All Matches →
            </button>
          </div>
        </div>
        
        <!-- Bookmarked Jobs -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Bookmarked Jobs</h3>
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

          <div class="flex justify-end">
            <button @click="showJobModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    
  </div>
  
  <!-- Create Job Modal -->
  <div v-if="showCreateJobModal" class="fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl border-4 border-gray-300 shadow-2xl">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Post a Job</h3>
        <button @click="showCreateJobModal = false" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
          <input v-model="newJob.title" type="text" placeholder="Job title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
          <input v-model="newJob.company_name" type="text" placeholder="Company name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
          <input v-model="newJob.location" type="text" placeholder="Location (e.g. Manila, Remote)" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Work Type</label>
          <select v-model="newJob.work_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select...</option>
            <option value="full-time">Full-time</option>
            <option value="part-time">Part-time</option>
            <option value="internship">Internship</option>
            <option value="contract">Contract</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
          <input v-model="newJob.industry" type="text" placeholder="Industry" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
          <select v-model="newJob.experience_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Select...</option>
            <option value="entry">Entry</option>
            <option value="mid">Mid</option>
            <option value="senior">Senior</option>
            <option value="executive">Executive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
          <input v-model="newJob.salary_range" type="text" placeholder="e.g. 40k-60k" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Required Skills (comma-separated)</label>
          <input v-model="requiredSkillsInput" type="text" placeholder="e.g. Vue, Laravel, MySQL" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
          <textarea v-model="newJob.description" rows="4" placeholder="Job description..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
        </div>
      </div>

      <div class="flex justify-end space-x-2 mt-6">
        <button @click="showCreateJobModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancel
        </button>
        <button @click="createJob" :disabled="!newJob.title || !newJob.description" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
          Post Job
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../config/api'

// Reactive data
const searchQuery = ref('')
const selectedLocation = ref('')
const selectedWorkType = ref('')
const selectedIndustry = ref('')
const selectedExperience = ref('')
const selectedJob = ref(null)
const showJobModal = ref(false)
const showCreateJobModal = ref(false)
const activeTab = ref('overview')
const loading = ref(false)

// Fetch jobs from API
const jobs = ref([])

const fetchJobs = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value || undefined,
      location: selectedLocation.value || undefined,
      work_type: selectedWorkType.value || undefined,
      industry: selectedIndustry.value || undefined,
      experience_level: selectedExperience.value || undefined
    }
    
    const response = await axios.get('/api/job-posts', { params })
    if (response?.data?.success && Array.isArray(response.data.data)) {
      jobs.value = response.data.data
    } else {
      jobs.value = []
    }
  } catch (error) {
    console.error('Error fetching jobs:', error)
    jobs.value = []
  } finally {
    loading.value = false
  }
}

const savedJobs = ref([])
const recommendedJobs = ref([])
const userJobPosts = ref([])
const jobApplicants = ref([])
// Career Matching
const careerMatches = ref([])
const matchingLoading = ref(false)
// removed apply flow state

// New job form state
const newJob = ref({
  title: '',
  description: '',
  company_name: '',
  location: '',
  work_type: '',
  required_skills: [],
  industry: '',
  experience_level: '',
  salary_range: ''
})
const requiredSkillsInput = ref('')

const jobDetailTabs = [
  { key: 'overview', label: 'Overview' },
  { key: 'company', label: 'About the Company' },
  { key: 'applicants', label: 'Applicants' }
]

// Computed properties
const filteredJobs = computed(() => {
  // Since we're filtering on the backend, just return the jobs
  return jobs.value
})

// Methods
const selectJob = (job) => {
  selectedJob.value = job
  showJobModal.value = true
  activeTab.value = 'overview'
}

// removed apply flow methods

// Save/Unsave jobs via localStorage
const STORAGE_KEY = 'saved_jobs'
const loadSavedJobs = () => {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    savedJobs.value = raw ? JSON.parse(raw) : []
  } catch {
    savedJobs.value = []
  }
}
const persistSavedJobs = () => {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(savedJobs.value))
}
const isJobSaved = (job) => savedJobs.value.some(j => j.job_id === job.job_id)
const toggleSaveJob = (job) => {
  if (isJobSaved(job)) {
    savedJobs.value = savedJobs.value.filter(j => j.job_id !== job.job_id)
  } else {
    savedJobs.value.unshift({ job_id: job.job_id, title: job.title, company_name: job.company_name })
  }
  persistSavedJobs()
}

// Create job via backend
const createJob = async () => {
  try {
    newJob.value.required_skills = requiredSkillsInput.value
      ? requiredSkillsInput.value.split(',').map(s => s.trim()).filter(Boolean)
      : []
    const response = await axios.post('/api/job-posts', newJob.value)
    if (response?.data?.success) {
      showCreateJobModal.value = false
      newJob.value = { title: '', description: '', company_name: '', location: '', work_type: '', required_skills: [], industry: '', experience_level: '', salary_range: '' }
      requiredSkillsInput.value = ''
      await fetchJobs()
    }
  } catch (error) {
    console.error('Error creating job:', error)
  }
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

// Career Matching
const getMatchPercentage = (match) => {
  if (match.match_percentage !== undefined) {
    return match.match_percentage
  }
  if (match.similarity_score !== undefined) {
    return Math.round(match.similarity_score * 100)
  }
  if (match.job?.similarity_score !== undefined) {
    return Math.round(match.job.similarity_score * 100)
  }
  return 'N/A'
}

const loadCareerMatching = async () => {
  matchingLoading.value = true
  try {
    const response = await axios.get('/api/jobs/quick-recommendations', {
      params: { limit: 5 }
    })
    
    if (response.data.success) {
      // Transform data to match expected format
      const matches = response.data.data || []
      careerMatches.value = matches.map(match => {
        // Handle both quick-recommendations format (just job objects) and detailed format
        if (match.job) {
          return match // Already in detailed format
        } else {
          // Quick format - transform to match structure
          return {
            job: match,
            similarity_score: match.similarity_score || 0,
            match_percentage: match.similarity_score ? Math.round(match.similarity_score * 100) : 0,
            matched_skills: [],
            missing_skills: [],
            bonus_skills: []
          }
        }
      })
    }
  } catch (error) {
    console.error('Error loading career matching:', error)
    careerMatches.value = []
  } finally {
    matchingLoading.value = false
  }
}

const viewAllCareerMatches = async () => {
  matchingLoading.value = true
  try {
    const response = await axios.get('/api/jobs/recommended', {
      params: { limit: 20 }
    })
    
    if (response.data.success) {
      // Detailed recommendations with match details
      careerMatches.value = response.data.data || []
    }
  } catch (error) {
    console.error('Error loading all career matches:', error)
  } finally {
    matchingLoading.value = false
  }
}

onMounted(() => {
  fetchJobs()
  loadSavedJobs()
  loadCareerMatching()
})

// Watch for filter changes and refetch
watch([searchQuery, selectedLocation, selectedWorkType, selectedIndustry, selectedExperience], () => {
  fetchJobs()
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