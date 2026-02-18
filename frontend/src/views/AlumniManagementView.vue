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

        <div class="grid grid-cols-1 gap-6">
          <div class="border border-gray-200 rounded-lg p-4 md:p-6">
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
            
            <div v-if="loadingVerifications" class="text-center py-8 text-gray-500">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
              <p>Loading verification requests...</p>
            </div>
            <div v-else class="overflow-x-auto">
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

        </div>
    </div>

      <div v-if="activeTab === 'employee-verification'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Employee/Faculty ID Verification Queue</h2>
        </div>

        <div class="flex items-center gap-4 mb-6">
          <div class="flex-1">
            <input 
              v-model="employeeVerificationSearch"
              type="text" 
              placeholder="Search by name, program, batch..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select v-model="employeeVerificationStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="unverified">Unverified</option>
          </select>
        </div>

        <div v-if="loadingVerifications" class="text-center py-8 text-gray-500">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p>Loading verification requests...</p>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program & Batch</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Photo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="request in filteredEmployeeVerificationRequests" 
                :key="request.id"
                class="hover:bg-gray-50"
              >
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ request.employee_id_number || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    request.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    request.status === 'verified' ? 'bg-green-100 text-green-800' :
                    'bg-red-100 text-red-800'
                  ]">
                    {{ request.status === 'verified' ? 'Verified' : request.status === 'unverified' ? 'Unverified' : 'Pending' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span v-if="request.id_image_path" class="text-gray-700">On file</span>
                  <span v-else class="text-gray-400">None</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button 
                      @click.stop="approveEmployeeVerification(request)"
                      :class="request.status === 'verified' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'"
                    >
                      {{ request.status === 'verified' ? 'Unverified' : 'Verified' }}
                    </button>
                    <button 
                      @click.stop="openEmployeeIdModal(request)"
                      class="text-blue-600 hover:text-blue-900"
                      :disabled="!request.id_image_path"
                    >
                      View
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredEmployeeVerificationRequests.length === 0">
                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                  No employee or faculty verification requests found.
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
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900">{{ job.title }}</h3>
                <p class="text-sm text-gray-600">{{ job.company_name }}</p>
              </div>
              <span class="bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-medium">
                Pending Review
              </span>
            </div>

            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
              <p class="text-sm font-medium text-gray-700">Posted by:</p>
              <p class="text-sm text-gray-900">
                {{ job.poster?.first_name }} {{ job.poster?.last_name }}
                <span v-if="job.poster?.email" class="text-gray-600">({{ job.poster.email }})</span>
              </p>
            </div>

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

            <div class="mb-4">
              <p class="text-sm font-medium text-gray-700 mb-2">Description:</p>
              <p class="text-sm text-gray-600 line-clamp-3">{{ job.description }}</p>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
              <button 
                @click="requestJobAction('approve', job)"
                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium"
              >
                Approve
              </button>
              <button 
                @click="requestJobAction('reject', job)"
                class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium"
              >
                Reject
              </button>
            </div>
          </div>
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
      <div v-if="loadingDirectory" class="text-center py-8 text-gray-500">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p>Loading users...</p>
      </div>
      <div v-else class="overflow-x-auto">
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
                  <div class="flex items-center space-x-2">
                    <button 
                      @click="editUser(user)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Edit
                    </button>
                    <button 
                      @click="requestDirectoryAction('reset_password', user)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Reset Password
                    </button>
                    <button 
                      @click="requestDirectoryAction(user.status === 'active' ? 'deactivate' : 'activate', user)"
                      :class="[
                        'hover:text-gray-900',
                        user.status === 'active' ? 'text-red-600' : 'text-green-600'
                      ]"
                    >
                      {{ user.status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                    <div class="relative" @click.stop>
                      <button
                        @click.stop="toggleRoleMenu(user)"
                        class="w-8 h-8 rounded-full hover:bg-gray-100 text-gray-600 flex items-center justify-center"
                        aria-label="More actions"
                      >
                        ⋮
                      </button>
                      <div
                        v-if="openRoleMenuId === user.id"
                        class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                        @click.stop
                      >
                        <button
                          v-if="shouldShowMakeAdmin(user)"
                          @click="openRoleChangeModal(user, 'admin')"
                          class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                          Make Admin?
                        </button>
                        <button
                          v-if="shouldShowRemoveAdmin(user)"
                          @click="openRoleChangeModal(user, 'alumni')"
                          class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                          Remove Admin Privileges?
                        </button>
                      </div>
                    </div>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Reports & Moderation Tab -->
      <div v-if="activeTab === 'reports'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Reports & Moderation</h2>
        </div>

        <!-- Reports Table -->
        <div v-if="loadingReports" class="text-center py-8 text-gray-500">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p>Loading reports...</p>
        </div>
        <div v-else class="overflow-x-auto">
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
                  {{ formatReportType(report.reported_entity_type) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ report.reporter ? `${report.reporter.first_name} ${report.reporter.last_name}` : 'N/A' }}
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
                      @click.stop="openResolveModal(report)"
                      class="text-orange-600 hover:text-orange-900"
                    >
                      Resolve
                    </button>
                    <button 
                      @click.stop="requestReportAction('dismiss', report)"
                      class="text-gray-600 hover:text-gray-900"
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

    </div>
  </div>

<!-- Edit User Modal -->
<div v-if="showEditModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showEditModal = false">
  <div class="bg-white rounded-lg w-full max-w-xl border-2 border-black shadow-2xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
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
<div v-if="showVerificationModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showVerificationModal = false">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-2xl w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
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

<div v-if="showEmployeeIdModal" class="fixed inset-0 bg-black/40 flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeEmployeeIdModal">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-3xl w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-xl font-semibold text-gray-900">Employee/Faculty ID Photo</h3>
      <button @click="closeEmployeeIdModal" class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <div class="p-6">
      <div class="mb-4">
        <p class="text-sm text-gray-600">{{ selectedEmployeeIdRequest?.full_name }}</p>
        <p class="text-sm text-gray-500">{{ selectedEmployeeIdRequest?.email }}</p>
      </div>
      <div class="flex items-center justify-center bg-gray-50 border border-gray-200 rounded-lg p-4 min-h-[200px]">
        <div v-if="employeeIdModalLoading" class="text-sm text-gray-500">Loading...</div>
        <img v-else-if="employeeIdModalUrl" :src="employeeIdModalUrl" alt="Employee ID" class="max-h-[70vh] max-w-full object-contain" />
        <p v-else class="text-sm text-gray-500">{{ employeeIdModalError || 'No ID image available.' }}</p>
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end">
      <button @click="closeEmployeeIdModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div v-if="showActionModal" class="fixed inset-0 bg-black/40 flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeActionModal">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-lg w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">{{ actionTitle }}</h3>
    </div>
    <div class="p-6 space-y-4">
      <p class="text-gray-700">{{ actionMessage }}</p>
      <div v-if="showActionReason" class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Reason for Denying</label>
        <textarea v-model="actionReason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter reason"></textarea>
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end space-x-2">
      <button @click="closeActionModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
      <button @click="confirmAction" :class="actionConfirmClass" class="px-4 py-2 text-white rounded-lg">Yes</button>
    </div>
  </div>
</div>

<!-- Reset Password Modal -->
<div v-if="showResetPasswordModal" class="fixed inset-0 bg-black/40 flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeResetPasswordModal">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-lg w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Reset Password</h3>
    </div>
    <div class="p-6 space-y-4">
      <p class="text-gray-700">
        Choose how to reset the password for {{ resetPasswordUser?.full_name }}.
      </p>
      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Temporary Password</label>
        <input v-model="resetPasswordValue" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter temporary password">
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end space-x-2">
      <button @click="closeResetPasswordModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
      <button @click="submitResetPassword" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Reset</button>
    </div>
  </div>
</div>

<div v-if="showRoleChangeModal" class="fixed inset-0 bg-black/40 flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeRoleChangeModal">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-lg w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">{{ roleChangeTitle }}</h3>
    </div>
    <div class="p-6 space-y-4">
      <p class="text-gray-700" v-if="roleChangeStep === 1">
        {{ roleChangeMessage }}
      </p>
      <div v-else class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Confirm admin password</label>
        <input v-model="roleChangePassword" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Enter your password">
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end space-x-2">
      <button v-if="roleChangeStep === 1" @click="closeRoleChangeModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
      <button v-if="roleChangeStep === 1" @click="proceedRoleChangeVerification" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Continue</button>
      <button v-if="roleChangeStep === 2" @click="closeRoleChangeModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Back</button>
      <button v-if="roleChangeStep === 2" @click="submitRoleChange" :disabled="roleChangeSubmitting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">Confirm</button>
    </div>
  </div>
</div>

<!-- Report View Modal -->
<div v-if="showReportModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showReportModal = false">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-2xl w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
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
          <p class="text-gray-900">{{ formatReportType(selectedReport.reported_entity_type) }}</p>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-500">Reported By</label>
          <p class="text-gray-900">{{ selectedReport.reporter ? `${selectedReport.reporter.first_name} ${selectedReport.reporter.last_name}` : 'N/A' }}</p>
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

      <div v-if="reportedEntityFields.length" class="space-y-2">
        <label class="text-sm font-medium text-gray-500">Reported Content</label>
        <div class="grid grid-cols-2 gap-4">
          <div v-for="field in reportedEntityFields" :key="field.label">
            <label class="text-sm font-medium text-gray-500">{{ field.label }}</label>
            <p class="text-gray-900">{{ field.value }}</p>
          </div>
        </div>
      </div>

      <div>
        <label class="text-sm font-medium text-gray-500">Reporter Notes</label>
        <p class="text-gray-900 mt-1">{{ formatReportReason(selectedReport.reason) }}</p>
        <p v-if="selectedReport.description" class="text-gray-900 mt-1 whitespace-pre-wrap">{{ selectedReport.description }}</p>
      </div>
      
      <div v-if="selectedReport.admin_notes">
        <label class="text-sm font-medium text-gray-500">Admin Notes</label>
        <p class="text-gray-900 mt-1 whitespace-pre-wrap">{{ selectedReport.admin_notes }}</p>
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end space-x-2">
      <button @click="showReportModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Close</button>
      <button v-if="selectedReport?.status === 'pending'" @click="requestReportAction('dismiss', selectedReport)" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Dismiss</button>
      <button v-if="selectedReport?.status === 'pending'" @click="openResolveModal(selectedReport)" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Resolve</button>
    </div>
  </div>
</div>

<div v-if="showResolveModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeResolveModal">
  <div class="bg-white rounded-lg border-2 border-black shadow-2xl max-w-lg w-full max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-xl font-semibold text-gray-900">Resolve Report</h3>
    </div>
    <div class="p-6 space-y-3">
      <p class="text-gray-700">Choose how you want to resolve this report.</p>
      <div class="flex flex-col space-y-2">
        <button @click="submitResolveAction('remove_content')" :disabled="resolveSubmitting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">Remove Content</button>
        <button @click="submitResolveAction('suspend_user')" :disabled="resolveSubmitting" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed">Suspend User</button>
        <button @click="submitResolveAction('issue_warning')" :disabled="resolveSubmitting" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed">Issue Warning</button>
      </div>
    </div>
    <div class="p-6 border-t border-gray-200 flex justify-end">
      <button @click="closeResolveModal" :disabled="resolveSubmitting" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Cancel</button>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import axios from '../config/api'
import { useToast } from '../composables/useToast'
import { useAuthStore } from '../stores/auth'

const toast = useToast()
const authStore = useAuthStore()

// Reactive data
const activeTab = ref('verification')
const verificationSearch = ref('')
const verificationProgram = ref('')
const verificationStatus = ref('')
const employeeVerificationSearch = ref('')
const employeeVerificationStatus = ref('')
const directorySearch = ref('')
const directoryRole = ref('')
const directoryStatus = ref('')
const selectAll = ref(false)
const selectedRequests = ref([])
const verificationRequests = ref([])
const employeeVerificationRequests = ref([])
const loadingVerifications = ref(false)
const showVerificationModal = ref(false)
const selectedVerificationRequest = ref(null)
const showEmployeeIdModal = ref(false)
const selectedEmployeeIdRequest = ref(null)
const employeeIdModalUrl = ref('')
const employeeIdModalLoading = ref(false)
const employeeIdModalError = ref('')
const showReportModal = ref(false)
const selectedReport = ref(null)
const reportedEntity = ref(null)
const showResolveModal = ref(false)
const resolveTargetReport = ref(null)
const resolveSubmitting = ref(false)
const showActionModal = ref(false)
const showResetPasswordModal = ref(false)
const resetPasswordMode = ref('temporary')
const resetPasswordValue = ref('')
const resetPasswordUser = ref(null)
const resetPasswordSubmitting = ref(false)
const openRoleMenuId = ref(null)
const showRoleChangeModal = ref(false)
const roleChangeStep = ref(1)
const roleChangeUser = ref(null)
const roleChangeTargetRole = ref('')
const roleChangePassword = ref('')
const roleChangeSubmitting = ref(false)
const actionType = ref('')
const actionScope = ref('')
const actionRequest = ref(null)
const actionReason = ref('')

const actionTitle = computed(() => {
  if (!actionRequest.value) return ''
  if (actionScope.value === 'verification') {
    return actionType.value === 'approve' ? 'Approve Verification' : 'Deny Verification'
  }
  if (actionScope.value === 'employee') {
    if (actionType.value === 'approve') return 'Mark Employee/Faculty as Verified'
    if (actionType.value === 'remove') return 'Mark Employee/Faculty as Unverified'
    return 'Employee/Faculty Submission'
  }
  if (actionScope.value === 'job') {
    if (actionType.value === 'approve') return 'Approve Job Post'
    if (actionType.value === 'reject') return 'Reject Job Post'
    return ''
  }
  if (actionScope.value === 'directory') {
    return actionType.value === 'deactivate' ? 'Deactivate User' : 'Activate User'
  }
  if (actionScope.value === 'report') {
    return actionType.value === 'resolve' ? 'Resolve Report' : 'Dismiss Report'
  }
  return ''
})

const actionMessage = computed(() => {
  if (!actionRequest.value) return ''
  if (actionScope.value === 'verification') {
    return actionType.value === 'approve'
      ? `Approve verification for ${actionRequest.value.full_name}?`
      : `Deny verification request for ${actionRequest.value.full_name}?`
  }
  if (actionScope.value === 'employee') {
    if (actionType.value === 'approve') {
      return `Mark ${actionRequest.value.full_name} as verified?`
    }
    if (actionType.value === 'remove') {
      return `Mark ${actionRequest.value.full_name} as unverified?`
    }
    return `Update employee/faculty status for ${actionRequest.value.full_name}?`
  }
  if (actionScope.value === 'job') {
    const title = actionRequest.value.title || 'this job post'
    if (actionType.value === 'approve') return `Approve "${title}"?`
    if (actionType.value === 'reject') return `Reject "${title}"?`
    return ''
  }
  if (actionScope.value === 'directory') {
    return actionType.value === 'deactivate'
      ? 'Are you sure you want to deactivate this user? They will lose access to all LCBAConnect+ features.'
      : `Activate ${actionRequest.value.full_name}?`
  }
  if (actionScope.value === 'report') {
    return actionType.value === 'resolve'
      ? `Resolve report #${actionRequest.value.id}?`
      : `Dismiss report #${actionRequest.value.id}?`
  }
  return ''
})

const roleChangeTitle = computed(() => {
  if (!roleChangeUser.value) return 'Role Change'
  return roleChangeTargetRole.value === 'admin' ? 'Make Admin' : 'Remove Admin Privileges'
})

const roleChangeMessage = computed(() => {
  if (!roleChangeUser.value) return ''
  if (roleChangeTargetRole.value === 'admin') {
    return `Grant admin privileges to ${roleChangeUser.value.full_name}?`
  }
  return `Remove admin privileges from ${roleChangeUser.value.full_name}?`
})

const showActionReason = computed(() => {
  return actionType.value === 'deny' && actionScope.value === 'verification'
})

const actionConfirmClass = computed(() => {
  if (actionType.value === 'dismiss') {
    return 'bg-gray-600 hover:bg-gray-700'
  }
  if (['deny', 'reject', 'deactivate', 'remove'].includes(actionType.value)) {
    return 'bg-red-600 hover:bg-red-700'
  }
  return 'bg-green-600 hover:bg-green-700'
})

const reportedEntityFields = computed(() => {
  if (!selectedReport.value) return []
  const type = selectedReport.value.reported_entity_type
  const entity = reportedEntity.value
  if (!entity) {
    return [
      { label: 'Entity ID', value: selectedReport.value.reported_entity_id || 'N/A' },
      { label: 'Details', value: 'Reported content is no longer available.' }
    ]
  }
  if (type === 'job_post') {
    return [
      { label: 'Title', value: entity.title || 'N/A' },
      { label: 'Company', value: entity.company_name || 'N/A' },
      { label: 'Location', value: entity.location || 'N/A' },
      { label: 'Status', value: entity.status || 'N/A' }
    ]
  }
  if (type === 'event') {
    return [
      { label: 'Title', value: entity.title || 'N/A' },
      { label: 'Location', value: entity.location || 'N/A' },
      { label: 'Start Date', value: entity.start_date ? new Date(entity.start_date).toLocaleString() : 'N/A' },
      { label: 'End Date', value: entity.end_date ? new Date(entity.end_date).toLocaleString() : 'N/A' }
    ]
  }
  if (type === 'user') {
    const fullName = `${entity.first_name || ''} ${entity.last_name || ''}`.trim()
    return [
      { label: 'Name', value: fullName || 'N/A' },
      { label: 'Email', value: entity.email || 'N/A' },
      { label: 'Role', value: entity.role || 'N/A' }
    ]
  }
  if (type === 'post') {
    return [
      { label: 'Post ID', value: entity.post_id || entity.id || 'N/A' },
      { label: 'User ID', value: entity.user_id || 'N/A' },
      { label: 'Content', value: entity.content || 'N/A' }
    ]
  }
  if (type === 'comment') {
    return [
      { label: 'Comment ID', value: entity.comment_id || entity.id || 'N/A' },
      { label: 'User ID', value: entity.user_id || 'N/A' },
      { label: 'Content', value: entity.content || 'N/A' }
    ]
  }
  return []
})

// Load verification requests from database
const loadVerificationRequests = async () => {
  loadingVerifications.value = true
  try {
    const response = await axios.get('/api/users', {
      params: { role: 'alumni', refresh: Date.now() }
    })
    const employeeResponse = await axios.get('/api/users', {
      params: { refresh: Date.now() }
    })
    if (response.data.success) {
      const users = response.data.data || []
      const employeeUsers = employeeResponse.data?.success ? (employeeResponse.data.data || []) : users
      verificationRequests.value = users.map(user => ({
        id: user.id,
        first_name: user.first_name,
        last_name: user.last_name,
        full_name: `${user.first_name} ${user.last_name}`,
        email: user.email,
        program: user.program || 'N/A',
        batch: user.batch || 'N/A',
        status: user.is_verified ? 'approved' : 'pending',
        submitted_at: user.created_at,
        is_verified: user.is_verified
      }))
      employeeVerificationRequests.value = employeeUsers
        .filter(user => user.lcba_employee_id)
        .map(user => {
          const rawStatus = user.lcba_verification_status || (user.is_lcba_employee_faculty ? 'verified' : 'pending')
          const status = rawStatus === 'rejected' ? 'unverified' : rawStatus
          return ({
          id: user.id,
          first_name: user.first_name,
          last_name: user.last_name,
          full_name: `${user.first_name} ${user.last_name}`,
          email: user.email,
          program: user.program || 'N/A',
          batch: user.batch || 'N/A',
          status,
          employee_id_number: user.id,
          id_image_path: user.lcba_employee_id || '',
          submitted_at: user.created_at
          })
        })
      // Update tab count
      const verTab = tabs.value.find(t => t.key === 'verification')
      if (verTab) verTab.count = verificationRequests.value.filter(r => r.status === 'pending').length
      const employeeTab = tabs.value.find(t => t.key === 'employee-verification')
      if (employeeTab) employeeTab.count = employeeVerificationRequests.value.filter(r => r.status === 'pending').length
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
const loadingDirectory = ref(false)

const reports = ref([])
const loadingReports = ref(false)

const tabs = ref([
  { key: 'verification', label: 'Verification Queue', count: 0 },
  { key: 'employee-verification', label: 'Employee/Faculty ID Queue', count: 0 },
  { key: 'job-approvals', label: 'Job Approvals', count: 0 },
  { key: 'directory', label: 'User Directory', count: 0 },
  { key: 'reports', label: 'Reports & Moderation', count: 0 },
  // { key: 'analytics', label: 'Analytics', count: null }, // Temporarily hidden
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

const filteredEmployeeVerificationRequests = computed(() => {
  let filtered = employeeVerificationRequests.value

  if (employeeVerificationSearch.value) {
    const search = employeeVerificationSearch.value.toLowerCase()
    filtered = filtered.filter(request => 
      request.full_name.toLowerCase().includes(search) ||
      request.program.toLowerCase().includes(search) ||
      request.batch.toString().includes(employeeVerificationSearch.value)
    )
  }

  if (employeeVerificationStatus.value) {
    filtered = filtered.filter(request => request.status === employeeVerificationStatus.value)
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

const openActionModal = (type, request, scope) => {
  if (!request) return
  if (scope === 'verification') {
    if (request.status !== 'pending' || request.is_verified) {
      toast.info(`${request.full_name} has already been approved.`, 'Info')
      return
    }
  } else if (scope === 'employee') {
    if (type === 'approve' && request.status === 'verified') {
      toast.info(`${request.full_name} is already verified.`, 'Info')
      return
    }
    if (type === 'remove' && request.status !== 'verified') {
      toast.info(`${request.full_name} is already unverified.`, 'Info')
      return
    }
  }
  actionType.value = type
  actionScope.value = scope
  actionRequest.value = request
  actionReason.value = ''
  showActionModal.value = true
}

const requestJobAction = (type, job) => {
  openActionModal(type, job, 'job')
}

const requestDirectoryAction = (type, user) => {
  if (type === 'reset_password') {
    openResetPasswordModal(user)
    return
  }
  openActionModal(type, user, 'directory')
}

const requestReportAction = (type, report) => {
  if (type === 'resolve') {
    openResolveModal(report)
    return
  }
  openActionModal(type, report, 'report')
}

const closeActionModal = () => {
  showActionModal.value = false
  actionType.value = ''
  actionScope.value = ''
  actionRequest.value = null
  actionReason.value = ''
}

const confirmAction = async () => {
  if (!actionRequest.value) return
  if (actionScope.value === 'verification') {
    if (actionRequest.value.status !== 'pending' || actionRequest.value.is_verified) {
      toast.info(`${actionRequest.value.full_name} has already been approved.`, 'Info')
      closeActionModal()
      return
    }
  } else if (actionScope.value === 'employee') {
    if (actionType.value === 'approve' && actionRequest.value.status === 'verified') {
      toast.info(`${actionRequest.value.full_name} is already verified.`, 'Info')
      closeActionModal()
      return
    }
    if (actionType.value === 'remove' && actionRequest.value.status !== 'verified') {
      toast.info(`${actionRequest.value.full_name} is already unverified.`, 'Info')
      closeActionModal()
      return
    }
  } else if (actionScope.value === 'directory') {
    if (actionType.value === 'activate' && actionRequest.value.status === 'active') {
      toast.info(`${actionRequest.value.full_name} is already active.`, 'Info')
      closeActionModal()
      return
    }
    if (actionType.value === 'deactivate' && actionRequest.value.status !== 'active') {
      toast.info(`${actionRequest.value.full_name} is already inactive.`, 'Info')
      closeActionModal()
      return
    }
  } else if (actionScope.value === 'report') {
    if (actionRequest.value.status !== 'pending') {
      toast.info('Report is already processed.', 'Info')
      closeActionModal()
      return
    }
  }
  if (actionType.value === 'deny' && actionScope.value === 'verification' && !actionReason.value.trim()) {
    toast.error('Please provide a reason for denial.', 'Validation Error')
    return
  }

  try {
    if (actionScope.value === 'verification') {
      if (actionType.value === 'approve') {
        const response = await axios.post(`/api/admin/users/${actionRequest.value.id}/approve`)
        if (response.data.success) {
          toast.success(`${actionRequest.value.full_name} has been approved and can now access the platform!`, 'Success')
          actionRequest.value.status = 'approved'
          actionRequest.value.is_verified = true
        }
      } else {
        const response = await axios.post(`/api/admin/users/${actionRequest.value.id}/reject`, { reason: actionReason.value })
        if (response.data.success) {
          toast.success(`${actionRequest.value.full_name}'s registration has been rejected and removed.`, 'Success')
        }
      }
    } else if (actionScope.value === 'employee') {
      if (actionType.value === 'approve') {
        const response = await axios.post(`/api/admin/users/${actionRequest.value.id}/employee-verify`)
        if (response.data.success) {
          toast.success(`${actionRequest.value.full_name} is now marked as verified.`, 'Success')
          actionRequest.value.status = 'verified'
        }
      } else if (actionType.value === 'remove') {
        const response = await axios.post(`/api/admin/users/${actionRequest.value.id}/employee-verify`, { action: 'remove' })
        if (response.data.success) {
          toast.success(`${actionRequest.value.full_name} is now marked as unverified.`, 'Success')
          actionRequest.value.status = 'unverified'
        }
      }
    } else if (actionScope.value === 'job') {
      if (actionType.value === 'approve') {
        await approveJob(actionRequest.value.job_id)
      } else if (actionType.value === 'reject') {
        await rejectJob(actionRequest.value.job_id)
      }
    } else if (actionScope.value === 'directory') {
      toggleUserStatus(actionRequest.value, actionType.value)
    } else if (actionScope.value === 'report') {
      if (actionType.value === 'resolve') {
        await resolveReport(actionRequest.value.id)
      } else {
        await dismissReport(actionRequest.value.id)
      }
      if (selectedReport.value?.id === actionRequest.value.id) {
        showReportModal.value = false
      }
    }
    if (actionScope.value === 'verification' || actionScope.value === 'employee') {
      await loadVerificationRequests()
    }
    closeActionModal()
  } catch (error) {
    const status = error.response?.status
    if (status === 403) {
      toast.error('Admin access required to approve this user.', 'Unauthorized')
      return
    }
    if (status === 404) {
      toast.info('User no longer exists or was already removed.', 'Info')
      await loadVerificationRequests()
      closeActionModal()
      return
    }
    toast.error('Failed to process request: ' + (error.response?.data?.message || error.message), 'Error')
  }
}

const approveRequest = (request) => {
  openActionModal('approve', request, 'verification')
}

const denyRequest = (request) => {
  openActionModal('deny', request, 'verification')
}

const approveEmployeeVerification = (request) => {
  if (request.status === 'verified') {
    openActionModal('remove', request, 'employee')
    return
  }
  openActionModal('approve', request, 'employee')
}

const openEmployeeIdModal = async (request) => {
  if (!request?.id_image_path) {
    toast.info('No ID image available for this user.', 'Info')
    return
  }
  selectedEmployeeIdRequest.value = request
  showEmployeeIdModal.value = true
  employeeIdModalError.value = ''
  employeeIdModalLoading.value = true
  if (employeeIdModalUrl.value) {
    URL.revokeObjectURL(employeeIdModalUrl.value)
    employeeIdModalUrl.value = ''
  }
  try {
    const response = await axios.get(`/api/admin/users/${request.id}/employee-id-image`, {
      responseType: 'blob'
    })
    employeeIdModalUrl.value = URL.createObjectURL(response.data)
  } catch (error) {
    employeeIdModalError.value = error.response?.data?.message || 'Unable to load ID image.'
  } finally {
    employeeIdModalLoading.value = false
  }
}

const closeEmployeeIdModal = () => {
  showEmployeeIdModal.value = false
  selectedEmployeeIdRequest.value = null
  employeeIdModalError.value = ''
  employeeIdModalLoading.value = false
  if (employeeIdModalUrl.value) {
    URL.revokeObjectURL(employeeIdModalUrl.value)
    employeeIdModalUrl.value = ''
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

const openResetPasswordModal = (user) => {
  resetPasswordUser.value = user
  resetPasswordMode.value = 'temporary'
  resetPasswordValue.value = ''
  showResetPasswordModal.value = true
}

const closeResetPasswordModal = () => {
  showResetPasswordModal.value = false
  resetPasswordMode.value = 'temporary'
  resetPasswordValue.value = ''
  resetPasswordUser.value = null
}

const submitResetPassword = async () => {
  if (!resetPasswordUser.value || resetPasswordSubmitting.value) return
  if (resetPasswordMode.value === 'temporary' && !resetPasswordValue.value.trim()) {
    toast.error('Temporary password is required.', 'Validation Error')
    return
  }
  resetPasswordSubmitting.value = true
  try {
    const payload = {
      mode: resetPasswordMode.value,
      temp_password: resetPasswordMode.value === 'temporary' ? resetPasswordValue.value.trim() : undefined
    }
    const response = await axios.post(`/api/admin/users/${resetPasswordUser.value.id}/reset-password`, payload)
    if (response.data.success) {
      toast.success('Password reset completed.', 'Success')
      closeResetPasswordModal()
    }
  } catch (error) {
    toast.error('Failed to reset password: ' + (error.response?.data?.message || error.message), 'Error')
  } finally {
    resetPasswordSubmitting.value = false
  }
}

const shouldShowMakeAdmin = (user) => {
  return user?.role === 'alumni' || user?.role === 'staff'
}

const shouldShowRemoveAdmin = (user) => {
  return user?.role === 'admin' && user?.id !== authStore.user?.id
}

const toggleRoleMenu = (user) => {
  if (!user?.id) return
  openRoleMenuId.value = openRoleMenuId.value === user.id ? null : user.id
}

const closeRoleMenu = () => {
  openRoleMenuId.value = null
}

const openRoleChangeModal = (user, targetRole) => {
  if (!user || !targetRole) return
  roleChangeUser.value = user
  roleChangeTargetRole.value = targetRole
  roleChangePassword.value = ''
  roleChangeStep.value = 1
  showRoleChangeModal.value = true
  closeRoleMenu()
}

const proceedRoleChangeVerification = () => {
  roleChangeStep.value = 2
}

const closeRoleChangeModal = () => {
  showRoleChangeModal.value = false
  roleChangeStep.value = 1
  roleChangeUser.value = null
  roleChangeTargetRole.value = ''
  roleChangePassword.value = ''
  roleChangeSubmitting.value = false
}

const submitRoleChange = async () => {
  if (!roleChangeUser.value || roleChangeSubmitting.value) return
  if (!roleChangePassword.value.trim()) {
    toast.error('Admin password is required.', 'Validation Error')
    return
  }
  roleChangeSubmitting.value = true
  try {
    const response = await axios.post(`/api/admin/users/${roleChangeUser.value.id}/role`, {
      role: roleChangeTargetRole.value,
      admin_password: roleChangePassword.value
    })
    if (response.data.success) {
      const updatedUser = response.data.data
      const idx = directoryUsers.value.findIndex(u => u.id === updatedUser.id)
      if (idx !== -1) {
        directoryUsers.value[idx] = { ...directoryUsers.value[idx], role: updatedUser.role }
      }
      toast.success('User role updated.', 'Success')
      closeRoleChangeModal()
    }
  } catch (error) {
    toast.error('Failed to update role: ' + (error.response?.data?.message || error.message), 'Error')
  } finally {
    roleChangeSubmitting.value = false
  }
}

const toggleUserStatus = async (user, action = null) => {
  const nextAction = action || (user.status === 'active' ? 'deactivate' : 'activate')
  const targetActive = nextAction === 'activate'
  try {
    const response = await axios.post(`/api/admin/users/${user.id}/status`, { is_active: targetActive })
    if (response.data.success) {
      const updatedUser = response.data.data
      user.status = updatedUser.status
      user.is_active = updatedUser.is_active
      const statusLabel = nextAction === 'activate' ? 'active' : (user.status === 'suspended' ? 'suspended' : 'inactive')
      toast.success(`${user.full_name} is now ${statusLabel}.`, 'Success')
    }
  } catch (error) {
    toast.error('Failed to update status: ' + (error.response?.data?.message || error.message), 'Error')
  }
}

const viewReport = async (report) => {
  selectedReport.value = report
  reportedEntity.value = null
  showReportModal.value = true
  try {
    const response = await axios.get(`/api/reports/${report.id}`)
    if (response.data.success) {
      selectedReport.value = response.data.data.report || report
      reportedEntity.value = response.data.data.reported_entity || null
    }
  } catch (error) {
    console.error('Error loading report details:', error)
  }
}

const openResolveModal = (report) => {
  if (!report || report.status !== 'pending') {
    toast.info('Report is already processed.', 'Info')
    return
  }
  if (!report.id) {
    toast.error('Unable to resolve report: missing report ID.', 'Error')
    return
  }
  resolveTargetReport.value = report
  showResolveModal.value = true
}

const closeResolveModal = () => {
  showResolveModal.value = false
  resolveTargetReport.value = null
}

const submitResolveAction = async (action) => {
  const reportId = resolveTargetReport.value?.id || selectedReport.value?.id
  if (!reportId) {
    toast.error('Unable to resolve report: missing report ID.', 'Error')
    return
  }
  resolveSubmitting.value = true
  try {
    const response = await axios.post(`/api/reports/${reportId}/resolve`, { action })
    if (response.data.success) {
      toast.success('Report resolved successfully', 'Success')
      await loadReports()
      if (selectedReport.value?.id === reportId) {
        showReportModal.value = false
      }
      closeResolveModal()
    }
  } catch (error) {
    console.error('Error resolving report:', error)
    toast.error('Failed to resolve report: ' + (error.response?.data?.message || error.message), 'Error')
  } finally {
    resolveSubmitting.value = false
  }
}

const warnUser = (report) => {
  console.log('Warn user for report:', report.id)
}

// Fetch users from backend
const fetchUsers = async () => {
  if (!authStore.isAuthenticated) {
    directoryUsers.value = []
    loadingDirectory.value = false
    return
  }
  loadingDirectory.value = true
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
        is_active: u.is_active !== undefined ? u.is_active : true,
        status: u.status === 'suspended'
          ? 'suspended'
          : (u.is_active === false ? 'inactive' : 'active')
      }))
      // update tab counts
      tabs.value = [
        { key: 'verification', label: 'Verification Queue', count: verificationRequests.value.filter(r => r.status === 'pending').length },
        { key: 'employee-verification', label: 'Employee/Faculty ID Queue', count: employeeVerificationRequests.value.filter(r => r.status === 'pending').length },
        { key: 'job-approvals', label: 'Job Approvals', count: pendingJobs.value.length },
        { key: 'directory', label: 'User Directory', count: directoryUsers.value.length },
        { key: 'reports', label: 'Reports & Moderation', count: reports.value.filter(r => r.status === 'pending').length },
        // { key: 'analytics', label: 'Analytics', count: null }, // Temporarily hidden
      ]
    } else {
      directoryUsers.value = []
    }
  } catch (e) {
    if (e.response?.status === 401) {
      directoryUsers.value = []
      return
    }
    console.error('Error fetching users:', e)
    directoryUsers.value = []
  } finally {
    loadingDirectory.value = false
  }
}

// Reports & Moderation Methods
const loadReports = async () => {
  if (!authStore.isAuthenticated) {
    reports.value = []
    return
  }
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
    if (error.response?.status === 401) {
      reports.value = []
      return
    }
    console.error('Error loading reports:', error)
  } finally {
    loadingReports.value = false
  }
}

const resolveReport = async (reportId, action = 'issue_warning') => {
  try {
    const response = await axios.post(`/api/reports/${reportId}/resolve`, { action })
    if (response.data.success) {
      toast.success('Report resolved successfully', 'Success')
      await loadReports()
    }
  } catch (error) {
    console.error('Error resolving report:', error)
    toast.error('Failed to resolve report: ' + (error.response?.data?.message || error.message), 'Error')
  }
}

const dismissReport = async (reportId) => {
  try {
    const response = await axios.put(`/api/reports/${reportId}`, { 
      status: 'dismissed',
      admin_notes: 'Report dismissed by admin'
    })
    if (response.data.success) {
      toast.success('Report dismissed', 'Success')
      await loadReports()
    }
  } catch (error) {
    console.error('Error dismissing report:', error)
    toast.error('Failed to dismiss report: ' + (error.response?.data?.message || error.message), 'Error')
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
    toast.error('Failed to update report', 'Error')
  }
}

// Helper function to format report type
const formatReportType = (type) => {
  if (!type) return 'N/A'
  
  const typeMap = {
    'job_post': 'Job Post',
    'user': 'User',
    'post': 'Post',
    'comment': 'Comment',
    'event': 'Event'
  }
  
  return typeMap[type] || type
}

const formatReportReason = (reason) => {
  if (!reason) return 'N/A'
  const reasonMap = {
    spam: 'Spam',
    inappropriate_content: 'Inappropriate content',
    scam_fraud: 'Scam or fraud',
    harassment: 'Harassment',
    false_information: 'False information',
    other: 'Other'
  }
  return reasonMap[reason] || reason
}

// Job Approvals Methods
const loadPendingJobs = async () => {
  if (!authStore.isAuthenticated) {
    pendingJobs.value = []
    return
  }
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
    if (error.response?.status === 401) {
      pendingJobs.value = []
      return
    }
    console.error('Error loading pending jobs:', error)
    toast.error('Failed to load pending jobs', 'Error')
  } finally {
    loadingJobs.value = false
  }
}

const approveJob = async (jobId) => {
  try {
    const response = await axios.post(`/api/admin/jobs/${jobId}/approve`)
    if (response.data.success) {
      toast.success('Job post approved successfully!', 'Success')
      await loadPendingJobs()
    }
  } catch (error) {
    console.error('Error approving job:', error)
    toast.error('Failed to approve job: ' + (error.response?.data?.message || error.message), 'Error')
  }
}

const rejectJob = async (jobId) => {
  const reason = ''
  try {
    const response = await axios.post(`/api/admin/jobs/${jobId}/reject`, { reason })
    if (response.data.success) {
      toast.success('Job post rejected', 'Success')
      await loadPendingJobs()
    }
  } catch (error) {
    console.error('Error rejecting job:', error)
    toast.error('Failed to reject job: ' + (error.response?.data?.message || error.message), 'Error')
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

const handleDocumentClick = () => {
  openRoleMenuId.value = null
}

onMounted(async () => {
  const isAuthenticated = await authStore.checkAuth()
  if (!isAuthenticated) {
    window.location.href = '/login'
    return
  }
  fetchUsers()
  loadPendingJobs()
  loadVerificationRequests()
  loadReports()
  document.addEventListener('click', handleDocumentClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick)
})

watch(activeTab, (newTab) => {
  if (!authStore.isAuthenticated) {
    return
  }
  if (newTab === 'job-approvals') {
    loadPendingJobs()
  } else if (newTab === 'verification' || newTab === 'employee-verification') {
    loadVerificationRequests()
  } else if (newTab === 'reports') {
    loadReports()
  }
})

watch([directoryRole, directorySearch], () => {
  if (!authStore.isAuthenticated) {
    return
  }
  fetchUsers()
})
</script>
