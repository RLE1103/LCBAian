<template>
  <div class="p-4 md:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 md:mb-6 gap-3">
      <h1 class="text-xl md:text-2xl font-bold text-gray-800">Jobs</h1>
    <div class="flex items-center space-x-2 md:space-x-4">
      <button @click="showCreateJobModal = true" class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-blue-700 text-sm md:text-base whitespace-nowrap">
        Post a Job
      </button>
        <button class="border border-gray-300 text-gray-700 px-3 md:px-4 py-2 rounded-lg hover:bg-gray-50 text-sm md:text-base whitespace-nowrap">
          <span class="hidden sm:inline">Bookmarked Jobs</span>
          <span class="sm:hidden">Bookmarks</span>
            </button>
          </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-4 md:mb-6">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="w-full lg:w-80 xl:w-96">
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
            <option v-for="location in filterOptions.locations" :key="location" :value="location">
              {{ location }}
            </option>
          </select>

          <select v-model="selectedWorkType" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Work Types</option>
            <option v-for="workType in filterOptions.workTypes" :key="workType" :value="workType">
              {{ workType }}
            </option>
          </select>

          <select v-model="selectedIndustry" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Industries</option>
            <option v-for="industry in filterOptions.industries" :key="industry" :value="industry">
              {{ industry }}
            </option>
          </select>

          <select v-model="selectedExperience" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Experience Levels</option>
            <option value="0-1">0-1 years (Entry Level)</option>
            <option value="1-3">1-3 years (Junior)</option>
            <option value="3-5">3-5 years (Mid Level)</option>
            <option value="5-10">5-10 years (Senior)</option>
            <option value="10+">10+ years (Expert/Lead)</option>
          </select>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - Job Listings -->
      <div class="lg:col-span-2">
        <div class="space-y-4">
          <div v-if="loading" class="text-center py-12 text-gray-500">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p>Loading jobs...</p>
          </div>
          <div v-else-if="filteredJobs.length === 0" class="text-center py-12 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
            </svg>
            <p>No jobs found matching your criteria</p>
            <p class="text-sm">Try adjusting your search or filters</p>
          </div>

          <div 
            v-for="job in paginatedJobs" 
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
                  <span v-if="job.status === 'approved'" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Verified
                  </span>
      </div>
                
                <p class="text-sm text-gray-600 mb-1">{{ job.company_name }}</p>
                <p v-if="job.poster" class="text-xs text-gray-500 mb-2">
                  Posted by: {{ job.poster.first_name }} {{ job.poster.last_name }}
                </p>
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
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
                    <div v-if="isAdmin" class="relative">
                      <button @click.stop="toggleJobMenu(job)" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100" title="Moderate">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                      </button>
                      <div v-if="activeJobMenuId === job.job_id" class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                        <button @click.stop="openModerationConfirm('remove_content', job)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Remove Content
                        </button>
                        <button @click.stop="openModerationConfirm('suspend_user', job)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Suspend User
                        </button>
                        <button @click.stop="openModerationConfirm('issue_warning', job)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Issue Warning
                        </button>
                      </div>
                    </div>
                    <button @click.stop="toggleSaveJob(job)" :title="isJobSaved(job) ? 'Unsave' : 'Save'" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                      </svg>
                    </button>
                    <button @click.stop="openReportModal(job)" title="Report" class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                      </svg>
                    </button>
                </div>
              </div>
            </div>
              </div>
            </div>
          </div>

        <!-- Pagination Controls -->
        <div v-if="totalPages > 1" class="mt-6 flex items-center justify-center gap-2">
          <button 
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            &lt;
          </button>
          
          <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-2 border rounded-lg min-w-[40px]',
              currentPage === page 
                ? 'bg-blue-600 text-white border-blue-600' 
                : 'border-gray-300 hover:bg-gray-50 text-gray-700'
            ]"
          >
            {{ page }}
          </button>
          
          <button 
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            &gt;
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
              @click="selectJob(match)"
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
                    <div
                      class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm"
                      :style="{ backgroundColor: getMatchStrength(match).bgColor }"
                    >
                      <span class="sr-only">{{ getMatchStrength(match).label }}</span>
                    </div>
                  </div>
                  <p class="text-xs font-semibold mt-1" :style="{ color: getMatchStrength(match).textColor }">
                    {{ getMatchStrength(match).label }}
                  </p>
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
                  @click.stop="selectJob(match)"
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
    <div v-if="showJobModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[90] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showJobModal = false">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto border-2 border-black shadow-2xl">
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
                <div>
              <h2 class="text-2xl font-bold text-gray-900">{{ selectedJob?.title }}</h2>
              <div class="flex flex-col gap-2 mt-2">
                <div class="flex items-center gap-4 text-gray-600">
                  <span class="font-medium">{{ selectedJob?.company_name }}</span>
                  <span>{{ selectedJob?.location }}</span>
                  <span>{{ formatTime(selectedJob?.created_at) }}</span>
                </div>
                <p v-if="selectedJob?.poster" class="text-sm text-gray-600">
                  Posted by: {{ selectedJob.poster.first_name }} {{ selectedJob.poster.last_name }}
                </p>
              </div>
              <div v-if="selectedMatch || selectedJob?.similarity_score !== undefined || selectedJob?.match_percentage !== undefined" class="mt-3 flex items-center gap-2">
                <span class="w-3 h-3 rounded-full" :style="{ backgroundColor: getMatchStrength(selectedMatch || selectedJob).bgColor }"></span>
                <span class="text-sm font-medium" :style="{ color: getMatchStrength(selectedMatch || selectedJob).textColor }">
                  {{ getMatchStrength(selectedMatch || selectedJob).label }}
                </span>
              </div>
              
              <!-- Apply Button -->
              <a 
                v-if="selectedJob?.application_link"
                :href="selectedJob.application_link"
                target="_blank"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Visit Site
              </a>
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
                <p class="text-gray-700 mb-6 whitespace-pre-wrap">{{ selectedJob?.description }}</p>
                
                <!-- Only show required_skills if exists -->
                <div v-if="selectedJob?.required_skills && selectedJob.required_skills.length > 0" class="mb-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Skills</h3>
                  <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li v-for="skill in selectedJob.required_skills" :key="skill">{{ skill }}</li>
                  </ul>
                </div>
                
                <!-- Show preferred_skills if exists -->
                <div v-if="selectedJob?.preferred_skills && selectedJob.preferred_skills.length > 0">
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Preferred Skills</h3>
                  <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li v-for="skill in selectedJob.preferred_skills" :key="skill">{{ skill }}</li>
                  </ul>
                </div>
                
                <div v-if="!selectedJob?.required_skills?.length && !selectedJob?.preferred_skills?.length" class="text-gray-500 italic">
                  No specific skills requirements listed
                </div>
              </div>
            </div>

            <!-- About Company Tab -->
            <div v-if="activeTab === 'company'">
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">About {{ selectedJob?.company_name }}</h3>
                
                <!-- Only show database fields -->
                <div v-if="selectedJob?.industry || selectedJob?.location || selectedJob?.work_type" class="grid grid-cols-2 gap-4 text-sm">
                  <div v-if="selectedJob?.industry">
                    <span class="font-medium text-gray-900">Industry:</span>
                    <span class="text-gray-700 ml-2">{{ selectedJob.industry }}</span>
                  </div>
                  <div v-if="selectedJob?.location">
                    <span class="font-medium text-gray-900">Location:</span>
                    <span class="text-gray-700 ml-2">{{ selectedJob.location }}</span>
                  </div>
                  <div v-if="selectedJob?.work_type">
                    <span class="font-medium text-gray-900">Work Type:</span>
                    <span class="text-gray-700 ml-2">{{ selectedJob.work_type }}</span>
                  </div>
                  <div v-if="selectedJob?.experience_level">
                    <span class="font-medium text-gray-900">Experience Level:</span>
                    <span class="text-gray-700 ml-2">{{ formatExperienceLevel(selectedJob.experience_level) }}</span>
                  </div>
                  <div v-if="selectedJob?.salary_range">
                    <span class="font-medium text-gray-900">Salary Range:</span>
                    <span class="text-gray-700 ml-2">{{ formatSalaryRange(selectedJob.salary_range) }}</span>
                  </div>
                </div>
                
                <p v-else class="text-gray-500 italic text-center py-4">
                  Limited company information available for this position.
                </p>
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
  <div v-if="showCreateJobModal" class="fixed inset-0 bg-transparent flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showCreateJobModal = false">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl border-2 border-black shadow-2xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto" @click.stop>
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
          <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level (Years)</label>
          <input v-model.number="newJob.experience_level" type="number" min="0" step="1" placeholder="e.g. 3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
          <div class="space-y-3">
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div>
                <label class="block text-xs text-gray-500 mb-1">Min</label>
                <input v-model.number="jobSalaryMin" type="number" :min="jobSalaryRangeMin" :max="jobSalaryMax" :step="jobSalaryStep" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
              </div>
              <div>
                <label class="block text-xs text-gray-500 mb-1">Max</label>
                    <input v-model.number="jobSalaryMax" type="number" :min="jobSalaryMin" :step="jobSalaryStep" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
              </div>
            </div>
            <div class="flex items-center justify-between text-sm text-gray-600">
              <span>{{ formatSalaryValue(jobSalaryMin) }}</span>
              <span>{{ formatSalaryValue(jobSalaryMax) }}</span>
            </div>
            <div class="space-y-2">
                  <input v-model.number="jobSalaryMin" type="range" :min="jobSalaryRangeMin" :max="jobSalaryVisualMax" :step="jobSalaryStep" class="w-full" />
                  <input v-model.number="jobSalaryMax" type="range" :min="jobSalaryRangeMin" :max="jobSalaryVisualMax" :step="jobSalaryStep" class="w-full" />
            </div>
          </div>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Application Link (Optional)</label>
          <input v-model="newJob.application_link" type="url" placeholder="https://example.com/apply" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
          <p class="text-xs text-gray-500 mt-1">Link where applicants can apply for this position</p>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Required Skills (comma-separated)</label>
          <input v-model="requiredSkillsInput" type="text" placeholder="e.g. Vue, Laravel, MySQL" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Skills (comma-separated, optional)</label>
          <input v-model="preferredSkillsInput" type="text" placeholder="e.g. Docker, AWS" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Description (contact information required)</label>
          <textarea v-model="newJob.description" rows="4" placeholder="Job description..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
        </div>
      </div>

      <div class="flex justify-end space-x-2 mt-6">
        <button @click="showCreateJobModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancel
        </button>
        <button @click="createJob" :disabled="isPostJobDisabled" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
          Post Job
        </button>
      </div>
    </div>
  </div>

  <!-- Report Modal -->
  <ReportModal 
    v-if="reportingJobId !== null"
    :isOpen="showReportModal" 
    :entityType="'job_post'" 
    :entityId="reportingJobId" 
    @close="showReportModal = false"
    @submitted="handleReportSubmitted"
  />

  <div v-if="showModerationConfirm" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[100] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeModerationConfirm">
    <div class="bg-white rounded-lg p-6 w-full max-w-md border-2 border-black shadow-2xl" @click.stop>
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">{{ moderationConfirmTitle }}</h3>
        <button @click="closeModerationConfirm" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <p class="text-sm text-gray-600 mb-6">{{ moderationConfirmMessage }}</p>
      <div class="flex justify-end space-x-2">
        <button @click="closeModerationConfirm" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
          Cancel
        </button>
        <button @click="confirmModerationAction" :disabled="moderationSubmitting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
          Confirm
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../config/api'
import { useAuthStore } from '../stores/auth'
import { useToast } from '../composables/useToast'
import ReportModal from '../components/ReportModal.vue'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

// Reactive data
const searchQuery = ref('')
const selectedLocation = ref('')
const selectedWorkType = ref('')
const selectedIndustry = ref('')
const selectedExperience = ref('')
const selectedJob = ref(null)
const selectedMatch = ref(null)
const showJobModal = ref(false)
const showCreateJobModal = ref(false)
const activeTab = ref('overview')
const loading = ref(false)

// Report Modal State
const showReportModal = ref(false)
const reportingJobId = ref(null)
const activeJobMenuId = ref(null)
const showModerationConfirm = ref(false)
const pendingModeration = ref(null)
const moderationSubmitting = ref(false)

// Filter options from database
const filterOptions = ref({
  locations: [],
  industries: [],
  workTypes: []
})

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

const fetchFilterOptions = async () => {
  try {
    const response = await axios.get('/api/job-posts/filter-options')
    if (response?.data?.success) {
      const data = response.data.data || {}
      filterOptions.value = {
        locations: data.locations || [],
        industries: data.industries || [],
        workTypes: data.work_types || []
      }
    }
  } catch (error) {
    console.error('Error fetching filter options:', error)
  }
}

const savedJobs = ref([])
const recommendedJobs = ref([])
const userJobPosts = ref([])
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
  preferred_skills: [],
  industry: '',
  experience_level: null,
  salary_range: '',
  application_link: ''
})
const jobSalaryMin = ref(0)
const jobSalaryMax = ref(0)
const jobSalaryRangeMin = 0
const jobSalaryRangeMax = 200000
const jobSalaryStep = 1000
const jobSalaryVisualMax = computed(() => {
  const max = Number(jobSalaryMax.value || 0)
  return Math.max(jobSalaryRangeMax, max)
})
const requiredSkillsInput = ref('')
const preferredSkillsInput = ref('')

const jobDetailTabs = [
  { key: 'overview', label: 'Overview' },
  { key: 'company', label: 'About the Company' }
]

// Pagination
const currentPage = ref(1)
const itemsPerPage = 10

// Computed properties
const isAdmin = computed(() => authStore.user?.role === 'admin')

const filteredJobs = computed(() => {
  if (isAdmin.value) {
    return jobs.value.filter((job) => job?.status !== 'archived')
  }
  return jobs.value.filter((job) => job?.status === 'approved')
})

const totalPages = computed(() => Math.ceil(filteredJobs.value.length / itemsPerPage))

const paginatedJobs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredJobs.value.slice(start, end)
})

const isPostJobDisabled = computed(() => {
  if (!newJob.value.title) return true
  if (!newJob.value.company_name) return true
  if (!newJob.value.location) return true
  if (!newJob.value.work_type) return true
  if (!newJob.value.industry) return true
  if (newJob.value.experience_level === null || newJob.value.experience_level === '' || Number.isNaN(Number(newJob.value.experience_level))) return true
  if (!newJob.value.salary_range) return true
  if (!requiredSkillsInput.value) return true
  if (!newJob.value.description) return true
  return false
})

const parseSalaryRange = (value) => {
  if (!value) return { min: 0, max: 0 }
  if (typeof value === 'number') return { min: value, max: value }
  const numbers = String(value).match(/\d+/g)
  if (!numbers || !numbers.length) return { min: 0, max: 0 }
  const parsed = numbers.map((num) => Number(num)).filter((num) => !Number.isNaN(num))
  if (parsed.length === 1) return { min: parsed[0], max: parsed[0] }
  const min = Math.min(parsed[0], parsed[1])
  const max = Math.max(parsed[0], parsed[1])
  return { min, max }
}

const formatSalaryValue = (value) => `₱${Number(value || 0).toLocaleString()}`

const formatSalaryRange = (value) => {
  if (!value) return ''
  const numbers = String(value).match(/\d+/g)
  if (!numbers || !numbers.length) return String(value)
  const { min, max } = parseSalaryRange(value)
  if (min === max) return formatSalaryValue(min)
  return `${formatSalaryValue(min)}–${formatSalaryValue(max)}`
}

const syncJobSalaryFromForm = () => {
  const { min, max } = parseSalaryRange(newJob.value.salary_range)
  jobSalaryMin.value = min
  jobSalaryMax.value = max
}

watch(
  () => newJob.value.salary_range,
  (value) => {
    const { min, max } = parseSalaryRange(value)
    if (min !== jobSalaryMin.value) {
      jobSalaryMin.value = min
    }
    if (max !== jobSalaryMax.value) {
      jobSalaryMax.value = max
    }
  }
)

watch(
  [jobSalaryMin, jobSalaryMax],
  ([minValue, maxValue]) => {
    let min = Number(minValue || 0)
    let max = Number(maxValue || 0)
    if (!newJob.value.salary_range && min === 0 && max === 0) {
      return
    }
    if (min > max) {
      const temp = min
      min = max
      max = temp
    }
    if (min !== jobSalaryMin.value) {
      jobSalaryMin.value = min
    }
    if (max !== jobSalaryMax.value) {
      jobSalaryMax.value = max
    }
    const formatted = `${min}-${max}`
    if (newJob.value.salary_range !== formatted) {
      newJob.value.salary_range = formatted
    }
  }
)

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Methods
const selectJob = (jobOrMatch) => {
  selectedJob.value = jobOrMatch?.job ? jobOrMatch.job : jobOrMatch
  selectedMatch.value = jobOrMatch?.job ? jobOrMatch : null
  showJobModal.value = true
  activeTab.value = 'overview'
}

// removed apply flow methods

// Save/Unsave jobs via backend (per user)
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

// Create job via backend
const createJob = async () => {
  const missingFields = []
  if (!newJob.value.title) missingFields.push('Title')
  if (!newJob.value.company_name) missingFields.push('Company')
  if (!newJob.value.location) missingFields.push('Location')
  if (!newJob.value.work_type) missingFields.push('Work Type')
  if (!newJob.value.industry) missingFields.push('Industry')
  if (newJob.value.experience_level === null || newJob.value.experience_level === '' || Number.isNaN(Number(newJob.value.experience_level))) missingFields.push('Experience Level')
  if (!newJob.value.salary_range) missingFields.push('Salary Range')
  if (!requiredSkillsInput.value) missingFields.push('Required Skills')
  if (!newJob.value.description) missingFields.push('Description')

  if (missingFields.length > 0) {
    toast.error(`Please fill in the required fields: ${missingFields.join(', ')}`, 'Validation Error')
    return
  }

  try {
    // Prepare required_skills array
    newJob.value.required_skills = requiredSkillsInput.value
      ? requiredSkillsInput.value.split(',').map(s => s.trim()).filter(Boolean)
      : []
    if (newJob.value.experience_level === '' || Number.isNaN(Number(newJob.value.experience_level))) {
      newJob.value.experience_level = null
    }
    newJob.value.preferred_skills = preferredSkillsInput.value
      ? preferredSkillsInput.value.split(',').map(s => s.trim()).filter(Boolean)
      : []

    const response = await axios.post('/api/job-posts', newJob.value)
    
    if (response?.data?.success) {
      // Show success message
      toast.success('Job posted successfully!', 'Success')
      
      // Reset form
      newJob.value = { 
        title: '', 
        description: '', 
        company_name: '', 
        location: '', 
        work_type: '', 
        required_skills: [], 
        preferred_skills: [],
        industry: '', 
        experience_level: null, 
        salary_range: '',
        application_link: ''
      }
      jobSalaryMin.value = 0
      jobSalaryMax.value = 0
      requiredSkillsInput.value = ''
      preferredSkillsInput.value = ''
      
      // Close modal
      showCreateJobModal.value = false
      
      // Refresh jobs list
      await fetchJobs()
    } else {
      toast.error('Failed to create job: ' + (response?.data?.message || 'Unknown error'), 'Error')
    }
  } catch (error) {
    console.error('Error creating job:', error)
    toast.error('Error creating job: ' + (error.response?.data?.message || error.message || 'Unknown error'), 'Error')
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

const formatExperienceLevel = (value) => {
  if (value === null || value === undefined || value === '') return ''
  const numericValue = Number(value)
  if (Number.isNaN(numericValue)) return value
  if (numericValue >= 10) return '10+ years (Expert/Lead)'
  if (numericValue >= 5) return '5-10 years (Senior)'
  if (numericValue >= 3) return '3-5 years (Mid Level)'
  if (numericValue >= 1) return '1-3 years (Junior)'
  return '0-1 years (Entry Level)'
}

const dedupeCareerMatches = (matches) => {
  const seen = new Set()
  return matches.filter((match) => {
    const jobId = match?.job?.job_id || match?.job_id
    if (!jobId || seen.has(jobId)) {
      return false
    }
    seen.add(jobId)
    return true
  })
}

// Career Matching
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

const loadCareerMatching = async () => {
  matchingLoading.value = true
  try {
    // Use detailed recommendations for better match information
    const response = await axios.get('/api/jobs/recommended', {
      params: { limit: 5 }
    })
    
    if (response.data.success) {
      // Detailed recommendations already have all the match information
      careerMatches.value = dedupeCareerMatches(response.data.data || [])
    } else {
      // Fallback to quick recommendations if detailed fails
      const quickResponse = await axios.get('/api/jobs/quick-recommendations', {
        params: { limit: 5 }
      })
      
      if (quickResponse.data.success) {
        // Transform quick format to match structure
        const matches = quickResponse.data.data || []
        const normalizedMatches = matches.map(match => {
          if (match.job) {
            return match
          } else {
            // Calculate match details on frontend if needed
            const userSkills = authStore.user?.skills || []
            const jobSkills = match.required_skills || []
            const matchedSkills = userSkills.filter(s => jobSkills.includes(s))
            const missingSkills = jobSkills.filter(s => !userSkills.includes(s))
            
            return {
              job: match,
              similarity_score: match.similarity_score || 0,
              match_percentage: match.similarity_score ? Math.round(match.similarity_score * 100) : 0,
              matched_skills: matchedSkills,
              missing_skills: missingSkills,
              bonus_skills: []
            }
          }
        })
        careerMatches.value = dedupeCareerMatches(normalizedMatches)
      }
    }
  } catch (error) {
    console.error('Error loading career matching:', error)
    careerMatches.value = []
  } finally {
    matchingLoading.value = false
  }
}

const viewAllCareerMatches = () => {
  router.push('/career-matching')
}

const openReportModal = (job) => {
  reportingJobId.value = job.job_id
  showReportModal.value = true
}

const toggleJobMenu = (job) => {
  activeJobMenuId.value = activeJobMenuId.value === job.job_id ? null : job.job_id
}

const openModerationConfirm = (action, job) => {
  pendingModeration.value = { action, job }
  showModerationConfirm.value = true
  activeJobMenuId.value = null
}

const closeModerationConfirm = () => {
  showModerationConfirm.value = false
  pendingModeration.value = null
}

const moderationConfirmTitle = computed(() => {
  if (!pendingModeration.value) return ''
  switch (pendingModeration.value.action) {
    case 'remove_content':
      return 'Remove Content'
    case 'suspend_user':
      return 'Suspend User'
    case 'issue_warning':
      return 'Issue Warning'
    default:
      return 'Confirm Action'
  }
})

const moderationConfirmMessage = computed(() => {
  if (!pendingModeration.value) return ''
  switch (pendingModeration.value.action) {
    case 'remove_content':
      return 'This will archive the job post and remove it from the feed.'
    case 'suspend_user':
      return 'This will immediately suspend the poster account.'
    case 'issue_warning':
      return 'This will issue a formal warning that must be acknowledged.'
    default:
      return 'Are you sure you want to proceed?'
  }
})

const confirmModerationAction = async () => {
  if (!pendingModeration.value) return
  moderationSubmitting.value = true
  const { action, job } = pendingModeration.value
  try {
    const response = await axios.post(`/api/job-posts/${job.job_id}/moderate`, { action })
    if (response?.data?.success) {
      toast.success('Action completed successfully', 'Success')
      await fetchJobs()
      closeModerationConfirm()
    } else {
      toast.error(response?.data?.message || 'Failed to complete action', 'Error')
    }
  } catch (error) {
    console.error('Error moderating job:', error)
    toast.error(error.response?.data?.message || error.message || 'Failed to complete action', 'Error')
  } finally {
    moderationSubmitting.value = false
  }
}

const handleReportSubmitted = () => {
  showReportModal.value = false
  reportingJobId.value = null
}

onMounted(() => {
  fetchJobs()
  fetchFilterOptions()
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
