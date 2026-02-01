<template>
  <div class="p-4 md:p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6 gap-3">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Alumni Management</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Manage alumni verification, reports, and analytics</p>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-md mb-4 md:mb-6">
      <div class="border-b border-gray-200 overflow-x-auto">
        <nav class="flex space-x-4 md:space-x-8 px-4 md:px-6 min-w-max">
          <button 
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'py-3 md:py-4 px-2 border-b-2 font-medium text-xs md:text-sm whitespace-nowrap',
              activeTab === tab.key 
                ? 'border-blue-600 text-blue-600' 
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
      <!-- Verification Queue Tab -->
      <div v-if="activeTab === 'verification'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Verification Queue</h2>
          <div class="flex items-center space-x-4">
            <button 
              @click="bulkApprove"
              class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
            >
              Bulk Approve
            </button>
            <button 
              @click="bulkDeny"
              class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
            >
              Bulk Deny
            </button>
          </div>
        </div>

        <!-- Search and Filters -->
        <div class="flex items-center gap-4 mb-6">
          <div class="flex-1">
            <input 
              v-model="verificationSearch"
              type="text" 
              placeholder="Search by name, program, batch..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select v-model="verificationProgram" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Programs</option>
            <option value="BSCS">BSCS</option>
            <option value="BSCpE">BSCpE</option>
            <option value="BSIT">BSIT</option>
          </select>
          <select v-model="verificationStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="denied">Denied</option>
          </select>
        </div>
        
        <!-- Verification Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program & Batch</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="request in filteredVerificationRequests" 
                :key="request.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <input 
                    type="checkbox" 
                    v-model="selectedRequests" 
                    :value="request.id"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                      {{ request.first_name[0] }}{{ request.last_name[0] }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ request.full_name }}</div>
                      <div class="text-sm text-gray-500">{{ request.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ request.program }} {{ request.batch }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    request.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    request.status === 'approved' ? 'bg-green-100 text-green-800' :
                    'bg-red-100 text-red-800'
                  ]">
                    {{ request.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button 
                      @click.stop="approveRequest(request)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Approve
          </button>
                    <button 
                      @click.stop="denyRequest(request)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Deny
          </button>
                    <button 
                      @click.stop="viewDetails(request)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View
          </button>
        </div>
                </td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>

      <!-- User Directory Tab -->
      <div v-if="activeTab === 'directory'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">User Directory</h2>
          <div class="flex items-center space-x-4">
            <button @click="exportDirectoryCsv" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
              Export CSV
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
              Add User
      </button>
    </div>
        </div>
      
        <!-- Search and Filters -->
      <div class="flex items-center gap-4 mb-6">
          <div class="flex-1">
          <input
              v-model="directorySearch"
            type="text"
              placeholder="Search by name, email, batch, program..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
          <select v-model="directoryRole" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Roles</option>
            <option value="alumni">Alumni</option>
            <option value="mentor">Mentor</option>
            <option value="admin">Admin</option>
          </select>
          <select v-model="directoryStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
      </div>

        <!-- Directory Table -->
      <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program & Batch</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="user in filteredDirectoryUsers" 
                :key="user.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                      {{ user.first_name[0] }}{{ user.last_name[0] }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ user.full_name }}</div>
                      <div class="text-sm text-gray-500">{{ user.headline || 'Alumni' }}</div>
                    </div>
                </div>
              </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ user.email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    user.role === 'admin' ? 'bg-red-100 text-red-800' :
                    user.role === 'mentor' ? 'bg-blue-100 text-blue-800' :
                    'bg-green-100 text-green-800'
                  ]">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ user.program }} {{ user.batch }}
              </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    user.status === 'active' ? 'bg-green-100 text-green-800' :
                    user.status === 'inactive' ? 'bg-gray-100 text-gray-800' :
                    'bg-red-100 text-red-800'
                  ]">
                    {{ user.status }}
                  </span>
              </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button 
                      @click="editUser(user)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Edit
                    </button>
                    <button 
                      @click="resetPassword(user)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Reset Password
                    </button>
                    <button 
                      @click="toggleUserStatus(user)"
                      :class="[
                        'hover:text-gray-900',
                        user.status === 'active' ? 'text-red-600' : 'text-green-600'
                      ]"
                    >
                      {{ user.status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Job Approvals Tab -->
      <div v-if="activeTab === 'job-approvals'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Job Post Approvals</h2>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">{{ pendingJobs.length }} pending</span>
            <button @click="loadPendingJobs" class="text-blue-600 hover:text-blue-800 text-sm">
              Refresh
            </button>
          </div>
        </div>

        <!-- Pending Jobs List -->
        <div v-if="loadingJobs" class="text-center py-8 text-gray-500">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p>Loading pending jobs...</p>
        </div>

        <div v-else-if="pendingJobs.length === 0" class="text-center py-12 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="font-medium">No pending job posts</p>
          <p class="text-sm">All job posts have been reviewed</p>
        </div>

        <div v-else class="space-y-4">
          <div 
            v-for="job in pendingJobs" 
            :key="job.job_id"
            class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow"
          >
            <!-- Job Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ job.title }}</h3>
                <p class="text-sm text-gray-600">{{ job.company_name }}</p>
              </div>
              <span class="bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-medium">
                Pending Review
              </span>
            </div>

            <!-- Posted By Info -->
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
              <p class="text-sm font-medium text-gray-700">Posted by:</p>
              <p class="text-sm text-gray-900">
                {{ job.poster?.first_name }} {{ job.poster?.last_name }}
                <span v-if="job.poster?.email" class="text-gray-600">({{ job.poster.email }})</span>
              </p>
            </div>

            <!-- Job Details Grid -->
            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
              <div>
                <span class="text-gray-600">Location:</span>
                <span class="text-gray-900 ml-2">{{ job.location || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-600">Work Type:</span>
                <span class="text-gray-900 ml-2">{{ job.work_type || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-600">Industry:</span>
                <span class="text-gray-900 ml-2">{{ job.industry || 'N/A' }}</span>
              </div>
              <div>
                <span class="text-gray-600">Experience Level:</span>
                <span class="text-gray-900 ml-2">{{ job.experience_level || 'N/A' }}</span>
              </div>
              <div class="col-span-2">
                <span class="text-gray-600">Application Link:</span>
                <a v-if="job.application_link" :href="job.application_link" target="_blank" class="text-blue-600 hover:underline ml-2">
                  {{ job.application_link }}
                </a>
                <span v-else class="text-gray-900 ml-2">Not provided</span>
              </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <p class="text-sm font-medium text-gray-700 mb-2">Description:</p>
              <p class="text-sm text-gray-600 line-clamp-3">{{ job.description }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
              <button 
                @click="approveJob(job.job_id)"
                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium"
              >
                Approve
              </button>
              <button 
                @click="rejectJob(job.job_id)"
                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium"
              >
                Reject
              </button>
              <button 
                @click="flagJob(job.job_id)"
                class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 font-medium"
              >
                Flag for Review
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Reports & Moderation Tab -->
      <div v-if="activeTab === 'reports'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Reports & Moderation</h2>
        </div>

        <!-- Reports Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reported By</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="report in reports" 
                :key="report.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ report.id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ report.type }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ report.reported_by }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ report.reason }}
              </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    report.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    report.status === 'resolved' ? 'bg-green-100 text-green-800' :
                    'bg-red-100 text-red-800'
                  ]">
                    {{ report.status }}
                  </span>
              </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button 
                      @click.stop="viewReport(report)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View
                    </button>
                    <button 
                      @click.stop="resolveReport(report.id)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Resolve
                    </button>
                    <button 
                      @click.stop="dismissReport(report.id)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Dismiss
                    </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      </div>

      <!-- Analytics Tab -->
      <div v-if="activeTab === 'analytics'" class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Analytics Dashboard</h2>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div class="bg-blue-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Alumni</p>
                <p class="text-2xl font-semibold text-gray-900">1,234</p>
              </div>
            </div>
          </div>

          <div class="bg-green-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Users</p>
                <p class="text-2xl font-semibold text-gray-900">856</p>
              </div>
            </div>
          </div>

          <div class="bg-yellow-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Jobs Posted</p>
                <p class="text-2xl font-semibold text-gray-900">89</p>
              </div>
            </div>
          </div>

          <div class="bg-purple-50 rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Messages Sent</p>
                <p class="text-2xl font-semibold text-gray-900">2,456</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Growth</h3>
            <div class="h-64 flex items-center justify-center text-gray-500">
              Chart placeholder - User growth over time
            </div>
          </div>
          <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Engagement Heatmap</h3>
            <div class="h-64 flex items-center justify-center text-gray-500">
              Chart placeholder - Feature usage heatmap
            </div>
          </div>
        </div>
      </div>

      <!-- Data Tools Tab -->
      <div v-if="activeTab === 'tools'" class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Data Tools</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Import Alumni Data -->
          <div class="border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Import Alumni Data</h3>
            <p class="text-gray-600 mb-4">Upload a CSV file to bulk import alumni information.</p>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload CSV File</label>
                <input type="file" accept=".csv" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
              </div>
              <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                Import Data
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Edit User Modal -->
<div v-if="showEditModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg w-full max-w-xl border-4 border-gray-300 shadow-2xl">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Edit User</h3>
        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
          <input v-model="editUserForm.first_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
          <input v-model="editUserForm.last_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input v-model="editUserForm.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
          <input v-model="editUserForm.program" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Batch</label>
          <input v-model="editUserForm.batch" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Headline</label>
          <input v-model="editUserForm.headline" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
          <input v-model="editUserForm.industry" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
          <input v-model="editUserForm.experience_level" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
        </div>
      </div>
      <div class="flex justify-end space-x-2 mt-6">
        <button @click="showEditModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
        <button @click="saveUserEdits" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
      </div>
    </div>
  </div>
</div>
  
<!-- Verification Request View Modal -->
<div v-if="showVerificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4" @click.self="showVerificationModal = false">
  <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Verification Request Details</h3>
    </div>
    <div v-if="selectedVerificationRequest" class="p-6 space-y-4">
      <div class="flex items-center gap-4 pb-4 border-b">
        <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-2xl">
          {{ selectedVerificationRequest.first_name[0] }}{{ selectedVerificationRequest.last_name[0] }}
        </div>
        <div>
          <h4 class="text-lg font-semibold">{{ selectedVerificationRequest.full_name }}</h4>
          <p class="text-sm text-gray-600">{{ selectedVerificationRequest.email }}</p>
        </div>
      </div>
      
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium text-gray-500">Program</label>
          <p class="text-gray-900">{{ selectedVerificationRequest.program }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Batch</label>
          <p class="text-gray-900">{{ selectedVerificationRequest.batch }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Status</label>
          <span :class="[
            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
            selectedVerificationRequest.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
            selectedVerificationRequest.status === 'approved' ? 'bg-green-100 text-green-800' :
            'bg-red-100 text-red-800'
          ]">
            {{ selectedVerificationRequest.status }}
          </span>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Submitted</label>
          <p class="text-gray-900">{{ new Date(selectedVerificationRequest.submitted_at).toLocaleDateString() }}</p>
        </div>
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end space-x-2">
      <button @click="showVerificationModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
      <button v-if="selectedVerificationRequest?.status === 'pending'" @click="denyRequest(selectedVerificationRequest); showVerificationModal = false" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Deny</button>
      <button v-if="selectedVerificationRequest?.status === 'pending'" @click="approveRequest(selectedVerificationRequest); showVerificationModal = false" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Approve</button>
    </div>
  </div>
</div>

<!-- Report View Modal -->
<div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4" @click.self="showReportModal = false">
  <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Report Details</h3>
    </div>
    <div v-if="selectedReport" class="p-6 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium text-gray-500">Report ID</label>
          <p class="text-gray-900">#{{ selectedReport.id }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Type</label>
          <p class="text-gray-900">{{ selectedReport.type }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Reported By</label>
          <p class="text-gray-900">{{ selectedReport.reported_by }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Status</label>
          <span :class="[
            'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
            selectedReport.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
            selectedReport.status === 'resolved' ? 'bg-green-100 text-green-800' :
            'bg-red-100 text-red-800'
          ]">
            {{ selectedReport.status }}
          </span>
        </div>
      </div>
      
      <div>
        <label class="text-sm font-medium text-gray-500">Reason</label>
        <p class="text-gray-900 mt-1">{{ selectedReport.reason }}</p>
      </div>
      
      <div v-if="selectedReport.description">
        <label class="text-sm font-medium text-gray-500">Description</label>
        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ selectedReport.description }}</p>
      </div>
      
      <div v-if="selectedReport.admin_notes">
        <label class="text-sm font-medium text-gray-500">Admin Notes</label>
        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ selectedReport.admin_notes }}</p>
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end space-x-2">
      <button @click="showReportModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
      <button v-if="selectedReport?.status === 'pending'" @click="dismissReport(selectedReport.id); showReportModal = false" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Dismiss</button>
      <button v-if="selectedReport?.status === 'pending'" @click="resolveReport(selectedReport.id); showReportModal = false" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Resolve</button>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../config/api'

// Reactive data
const activeTab = ref('verification')
const verificationSearch = ref('')
const verificationProgram = ref('')
const verificationStatus = ref('')
const directorySearch = ref('')
const directoryRole = ref('')
const directoryStatus = ref('')
const selectAll = ref(false)
const selectedRequests = ref([])
const verificationRequests = ref([])
const loadingVerifications = ref(false)
const showVerificationModal = ref(false)
const selectedVerificationRequest = ref(null)
const showReportModal = ref(false)
const selectedReport = ref(null)

// Load verification requests from database
const loadVerificationRequests = async () => {
  loadingVerifications.value = true
  try {
    const response = await axios.get('/api/users', {
      params: { role: 'alumni' }
    })
    if (response.data.success) {
      verificationRequests.value = response.data.data.map(user => ({
        id: user.id,
        first_name: user.first_name,
        last_name: user.last_name,
        full_name: `${user.first_name} ${user.last_name}`,
        email: user.email,
        program: user.program || 'N/A',
        batch: user.batch || 'N/A',
        status: user.lcba_verification_status || 'pending',
        submitted_at: user.created_at
      }))
      // Update tab count
      const verTab = tabs.value.find(t => t.key === 'verification')
      if (verTab) verTab.count = verificationRequests.value.filter(r => r.status === 'pending').length
    }
  } catch (error) {
    console.error('Error loading verification requests:', error)
  } finally {
    loadingVerifications.value = false
  }
}

const pendingJobs = ref([])
const loadingJobs = ref(false)

const directoryUsers = ref([])

const reports = ref([])
const loadingReports = ref(false)

const tabs = ref([
  { key: 'verification', label: 'Verification Queue', count: 0 },
  { key: 'job-approvals', label: 'Job Approvals', count: 0 },
  { key: 'directory', label: 'User Directory', count: 0 },
  { key: 'reports', label: 'Reports & Moderation', count: 0 },
  // { key: 'analytics', label: 'Analytics', count: null }, // Temporarily hidden
  { key: 'tools', label: 'Data Tools', count: null }
])

// Computed properties
const filteredVerificationRequests = computed(() => {
  let filtered = verificationRequests.value

  if (verificationSearch.value) {
    filtered = filtered.filter(request => 
      request.full_name.toLowerCase().includes(verificationSearch.value.toLowerCase()) ||
      request.program.toLowerCase().includes(verificationSearch.value.toLowerCase()) ||
      request.batch.includes(verificationSearch.value)
    )
  }

  if (verificationProgram.value) {
    filtered = filtered.filter(request => request.program === verificationProgram.value)
  }

  if (verificationStatus.value) {
    filtered = filtered.filter(request => request.status === verificationStatus.value)
  }

  return filtered
})

const filteredDirectoryUsers = computed(() => {
  let filtered = directoryUsers.value

  if (directorySearch.value) {
    filtered = filtered.filter(user => 
      user.full_name.toLowerCase().includes(directorySearch.value.toLowerCase()) ||
      user.email.toLowerCase().includes(directorySearch.value.toLowerCase()) ||
      user.program.toLowerCase().includes(directorySearch.value.toLowerCase()) ||
      user.batch.includes(directorySearch.value)
    )
  }

  if (directoryRole.value) {
    filtered = filtered.filter(user => user.role === directoryRole.value)
  }

  if (directoryStatus.value) {
    filtered = filtered.filter(user => user.status === directoryStatus.value)
  }

  return filtered
})

// Methods
const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedRequests.value = filteredVerificationRequests.value.map(request => request.id)
  } else {
    selectedRequests.value = []
  }
}

const bulkApprove = () => {
  if (selectedRequests.value.length === 0) return
  
  if (confirm(`Approve ${selectedRequests.value.length} verification requests?`)) {
    selectedRequests.value.forEach(id => {
      const request = verificationRequests.value.find(r => r.id === id)
      if (request) {
        request.status = 'approved'
      }
    })
    selectedRequests.value = []
    selectAll.value = false
  }
}

const bulkDeny = () => {
  if (selectedRequests.value.length === 0) return
  
  if (confirm(`Deny ${selectedRequests.value.length} verification requests?`)) {
    selectedRequests.value.forEach(id => {
      const request = verificationRequests.value.find(r => r.id === id)
      if (request) {
        request.status = 'denied'
      }
    })
    selectedRequests.value = []
    selectAll.value = false
  }
}

const approveRequest = (request) => {
  if (confirm(`Approve verification request for ${request.full_name}?`)) {
    request.status = 'approved'
  }
}

const denyRequest = (request) => {
  if (confirm(`Deny verification request for ${request.full_name}?`)) {
    request.status = 'denied'
  }
}


const viewDetails = (request) => {
  selectedVerificationRequest.value = request
  showVerificationModal.value = true
}

// Edit user modal
const showEditModal = ref(false)
const editUserForm = ref({ id: null, first_name: '', last_name: '', email: '', program: '', batch: '', headline: '', industry: '', experience_level: '' })
const editUser = (user) => {
  editUserForm.value = {
    id: user.id,
    first_name: user.first_name || '',
    last_name: user.last_name || '',
    email: user.email || '',
    program: user.program || '',
    batch: user.batch || '',
    headline: user.headline || '',
    industry: user.industry || '',
    experience_level: user.experience_level || ''
  }
  showEditModal.value = true
}
const saveUserEdits = async () => {
  try {
    const { id, ...payload } = editUserForm.value
    const response = await axios.put(`/api/users/${id}`, payload)
    if (response?.data?.success) {
      // update local row
      const idx = directoryUsers.value.findIndex(u => u.id === id)
      if (idx !== -1) {
        directoryUsers.value[idx] = { ...directoryUsers.value[idx], ...payload }
      }
      showEditModal.value = false
    }
  } catch (e) {
    console.error('Error updating user:', e)
  }
}

const resetPassword = (user) => {
  if (confirm(`Reset password for ${user.full_name}?`)) {
    console.log('Reset password for:', user.full_name)
  }
}

const toggleUserStatus = (user) => {
  const action = user.status === 'active' ? 'deactivate' : 'activate'
  if (confirm(`${action} ${user.full_name}?`)) {
    user.status = user.status === 'active' ? 'inactive' : 'active'
  }
}

const viewReport = (report) => {
  selectedReport.value = report
  showReportModal.value = true
}

const warnUser = (report) => {
  console.log('Warn user for report:', report.id)
}

// Fetch users from backend
const fetchUsers = async () => {
  try {
    const params = {
      role: directoryRole.value || undefined,
      search: directorySearch.value || undefined
    }
    const response = await axios.get('/api/users', { params })
    if (response?.data?.success && Array.isArray(response.data.data)) {
      directoryUsers.value = response.data.data.map(u => ({
        id: u.id,
        first_name: u.first_name,
        last_name: u.last_name,
        full_name: `${u.first_name} ${u.last_name}`,
        email: u.email,
        role: u.role,
        program: u.program,
        batch: u.batch,
        headline: u.headline,
        industry: u.industry,
        experience_level: u.experience_level,
        status: 'active'
      }))
      // update tab counts
      tabs.value = [
        { key: 'verification', label: 'Verification Queue', count: verificationRequests.value.filter(r => r.status === 'pending').length },
        { key: 'directory', label: 'User Directory', count: directoryUsers.value.length },
        { key: 'reports', label: 'Reports & Moderation', count: reports.value.filter(r => r.status === 'pending').length },
        // { key: 'analytics', label: 'Analytics', count: null }, // Temporarily hidden
        { key: 'tools', label: 'Data Tools', count: null }
      ]
    } else {
      directoryUsers.value = []
    }
  } catch (e) {
    console.error('Error fetching users:', e)
    directoryUsers.value = []
  }
}

// Reports & Moderation Methods
const loadReports = async () => {
  loadingReports.value = true
  try {
    const response = await axios.get('/api/reports')
    if (response.data.success) {
      reports.value = response.data.data.data || response.data.data || []
      // Update tab count
      const reportTab = tabs.value.find(t => t.key === 'reports')
      if (reportTab) reportTab.count = reports.value.filter(r => r.status === 'pending').length
    }
  } catch (error) {
    console.error('Error loading reports:', error)
  } finally {
    loadingReports.value = false
  }
}

const resolveReport = async (reportId) => {
  if (!confirm('Are you sure you want to resolve this report?')) return
  
  try {
    const response = await axios.post(`/api/reports/${reportId}/resolve`)
    if (response.data.success) {
      alert('Report resolved successfully')
      await loadReports()
    }
  } catch (error) {
    console.error('Error resolving report:', error)
    alert('Failed to resolve report: ' + (error.response?.data?.message || error.message))
  }
}

const dismissReport = async (reportId) => {
  if (!confirm('Are you sure you want to dismiss this report?')) return
  
  try {
    const response = await axios.put(`/api/reports/${reportId}`, { 
      status: 'dismissed',
      admin_notes: 'Report dismissed by admin'
    })
    if (response.data.success) {
      alert('Report dismissed')
      await loadReports()
    }
  } catch (error) {
    console.error('Error dismissing report:', error)
    alert('Failed to dismiss report: ' + (error.response?.data?.message || error.message))
  }
}

const updateReportStatus = async (reportId, status, notes = '') => {
  try {
    const response = await axios.put(`/api/reports/${reportId}`, { 
      status,
      admin_notes: notes
    })
    if (response.data.success) {
      await loadReports()
    }
  } catch (error) {
    console.error('Error updating report:', error)
    alert('Failed to update report')
  }
}

// Job Approvals Methods
const loadPendingJobs = async () => {
  loadingJobs.value = true
  try {
    const response = await axios.get('/api/admin/jobs/pending')
    if (response.data.success) {
      pendingJobs.value = response.data.data.data || response.data.data || []
      // Update tab count
      const jobTab = tabs.value.find(t => t.key === 'job-approvals')
      if (jobTab) jobTab.count = pendingJobs.value.length
    }
  } catch (error) {
    console.error('Error loading pending jobs:', error)
    alert('Failed to load pending jobs')
  } finally {
    loadingJobs.value = false
  }
}

const approveJob = async (jobId) => {
  if (!confirm('Are you sure you want to approve this job post?')) return
  
  try {
    const response = await axios.post(`/api/admin/jobs/${jobId}/approve`)
    if (response.data.success) {
      alert('Job post approved successfully!')
      await loadPendingJobs()
    }
  } catch (error) {
    console.error('Error approving job:', error)
    alert('Failed to approve job: ' + (error.response?.data?.message || error.message))
  }
}

const rejectJob = async (jobId) => {
  const reason = prompt('Please provide a reason for rejection (optional):')
  if (reason === null) return // User cancelled
  
  try {
    const response = await axios.post(`/api/admin/jobs/${jobId}/reject`, { reason })
    if (response.data.success) {
      alert('Job post rejected')
      await loadPendingJobs()
    }
  } catch (error) {
    console.error('Error rejecting job:', error)
    alert('Failed to reject job: ' + (error.response?.data?.message || error.message))
  }
}

const flagJob = async (jobId) => {
  if (!confirm('Flag this job post for further review?')) return
  
  try {
    const response = await axios.post(`/api/admin/jobs/${jobId}/flag`)
    if (response.data.success) {
      alert('Job post flagged for review')
      await loadPendingJobs()
    }
  } catch (error) {
    console.error('Error flagging job:', error)
    alert('Failed to flag job: ' + (error.response?.data?.message || error.message))
  }
}

// Export CSV for filtered directory
const exportDirectoryCsv = () => {
  const rows = filteredDirectoryUsers.value
  if (rows.length === 0) return
  const headers = ['id','first_name','last_name','email','role','program','batch','headline','industry','experience_level']
  const csv = [headers.join(',')]
  rows.forEach(r => {
    const vals = headers.map(h => {
      const val = (r[h] ?? '').toString().replace(/"/g,'""')
      return `"${val}` + `"`
    })
    csv.push(vals.join(','))
  })
  const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'alumni_directory.csv'
  a.click()
  URL.revokeObjectURL(url)
}

onMounted(() => {
  fetchUsers()
  loadPendingJobs()
  loadVerificationRequests()
  loadReports()
})

watch(activeTab, (newTab) => {
  if (newTab === 'job-approvals') {
    loadPendingJobs()
  } else if (newTab === 'verification') {
    loadVerificationRequests()
  } else if (newTab === 'reports') {
    loadReports()
  }
})

watch([directoryRole, directorySearch], () => {
  fetchUsers()
})
</script>