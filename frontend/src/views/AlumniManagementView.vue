<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Alumni Management</h1>
        <p class="text-gray-600 mt-1">Manage alumni verification, reports, and analytics</p>
      </div>
      <div class="flex items-center space-x-4">
        <button @click="exportDirectoryCsv" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
          Export Data
        </button>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
          Send Invitations
        </button>
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proof Submitted</th>
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
                  <button 
                    @click="previewProof(request)"
                    class="text-blue-600 hover:text-blue-800 text-sm underline"
                  >
                    {{ request.proof_file }}
                  </button>
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
                      @click="approveRequest(request)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Approve
          </button>
                    <button 
                      @click="denyRequest(request)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Deny
          </button>
                    <button 
                      @click="viewDetails(request)"
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

      <!-- Reports & Moderation Tab -->
      <div v-if="activeTab === 'reports'" class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-semibold text-gray-900">Reports & Moderation</h2>
          <div class="flex items-center space-x-4">
            <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50">
              Export Reports
            </button>
          </div>
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
                      @click="viewReport(report)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      View
                    </button>
                    <button 
                      @click="resolveReport(report)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Resolve
                    </button>
                    <button 
                      @click="warnUser(report)"
                      class="text-yellow-600 hover:text-yellow-900"
                    >
                      Warn
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

// Verification queue placeholder (no backend yet)
const verificationRequests = ref([
  {
    id: 1,
    first_name: 'Maria',
    last_name: 'Lopez',
    full_name: 'Maria Lopez',
    email: 'maria.lopez@example.com',
    program: 'BSCS',
    batch: '2020',
    proof_file: 'Transcript.pdf',
    status: 'pending',
    submitted_at: new Date('2025-01-15')
  },
  {
    id: 2,
    first_name: 'John',
    last_name: 'Cruz',
    full_name: 'John Cruz',
    email: 'john.cruz@example.com',
    program: 'BSCpE',
    batch: '2019',
    proof_file: 'Alumni ID.jpg',
    status: 'pending',
    submitted_at: new Date('2025-01-14')
  },
  {
    id: 3,
    first_name: 'Sarah',
    last_name: 'Martinez',
    full_name: 'Sarah Martinez',
    email: 'sarah.martinez@example.com',
    program: 'BSIT',
    batch: '2021',
    proof_file: 'Diploma.pdf',
    status: 'approved',
    submitted_at: new Date('2025-01-13')
  }
])

const directoryUsers = ref([])

const reports = ref([
  {
    id: 'RPT-1021',
    type: 'Message',
    reported_by: 'John Doe',
    reason: 'Harassment',
    status: 'pending'
  },
  {
    id: 'RPT-1022',
    type: 'Profile',
    reported_by: 'Jane Smith',
    reason: 'Inappropriate content',
    status: 'resolved'
  },
  {
    id: 'RPT-1023',
    type: 'Post',
    reported_by: 'Mike Johnson',
    reason: 'Spam',
    status: 'pending'
  }
])

const tabs = ref([
  { key: 'verification', label: 'Verification Queue', count: 0 },
  { key: 'directory', label: 'User Directory', count: 0 },
  { key: 'reports', label: 'Reports & Moderation', count: 0 },
  { key: 'analytics', label: 'Analytics', count: null },
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

const previewProof = (request) => {
  console.log('Preview proof for:', request.full_name)
}

const viewDetails = (request) => {
  console.log('View details for:', request.full_name)
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
  console.log('View report:', report.id)
}

const resolveReport = (report) => {
  if (confirm(`Mark report ${report.id} as resolved?`)) {
    report.status = 'resolved'
  }
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
        { key: 'analytics', label: 'Analytics', count: null },
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
})

watch([directoryRole, directorySearch], () => {
  fetchUsers()
})
</script>