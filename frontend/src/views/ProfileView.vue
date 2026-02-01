<template>
  <div class="p-4 md:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 md:mb-6 gap-3">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">My Profile</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Manage your information and privacy settings</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="saveProfile" :disabled="saving" class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm md:text-base">
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
      <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
              <input v-model="form.first_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name <span class="text-gray-400 text-xs">(Optional)</span></label>
              <input v-model="form.middle_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
              <input v-model="form.last_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Suffix <span class="text-gray-400 text-xs">(Optional)</span></label>
              <input v-model="form.suffix" type="text" placeholder="Jr., Sr., III, etc." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Birthday <span class="text-gray-400 text-xs">(Optional)</span></label>
              <input v-model="form.birthdate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Headline</label>
              <input v-model="form.headline" type="text" placeholder="e.g., Junior Developer at TechStart" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>
        </div>

        <!-- Contact & Socials -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact & Socials</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
                <span class="text-xs text-gray-500">(Privacy controlled in settings below)</span>
              </label>
              <input 
                v-model="form.email" 
                type="email" 
                disabled
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed"
              />
              <p class="text-xs text-gray-500 mt-1">
                This is your login email. Visibility is controlled by your privacy settings.
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Profile URL</label>
              <input v-model="form.linkedin_url" type="url" placeholder="https://linkedin.com/in/yourprofile" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Personal Portfolio/Website URL</label>
              <input v-model="form.portfolio_url" type="url" placeholder="https://yourportfolio.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>
        </div>

        <!-- Education History -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Education History</h2>
            <button @click="showEducationModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
              + Add Education
            </button>
          </div>

          <!-- Current LCBA Education (from program/batch fields) -->
          <div v-if="form.program || form.batch" class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-sm font-medium text-gray-900">🎓 LCBA - {{ form.program || 'Program' }}</span>
                  <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">LCBA</span>
                </div>
                <p class="text-xs text-gray-600">Graduated: {{ form.batch || 'Year not specified' }}</p>
              </div>
            </div>
          </div>

          <!-- Education History Timeline -->
          <div v-if="educationHistory.length > 0" class="space-y-3">
            <div v-for="edu in educationHistory" :key="edu.id" class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm font-medium text-gray-900">{{ getLevelLabel(edu.level) }} - {{ edu.school_name }}</span>
                    <span v-if="edu.is_lcba" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">LCBA</span>
                  </div>
                  <p v-if="edu.year_graduated" class="text-xs text-gray-600">Graduated: {{ edu.year_graduated }}</p>
                  <p v-if="edu.awards" class="text-xs text-gray-500 mt-1">🏆 {{ edu.awards }}</p>
                </div>
                <div class="flex items-center gap-2">
                  <button @click="editEducation(edu)" class="text-blue-600 hover:text-blue-800 text-xs">
                    Edit
                  </button>
                  <button @click="deleteEducation(edu.id)" class="text-red-600 hover:text-red-800 text-xs">
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-gray-500">
            <p class="text-sm">No additional education history added yet.</p>
            <p class="text-xs mt-1">Click "Add Education" to start building your educational timeline.</p>
          </div>

          <!-- Highest Attainment -->
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Highest Educational Attainment</label>
            <select v-model="form.highest_educational_attainment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">Select attainment</option>
              <option value="elementary">Elementary Graduate</option>
              <option value="high_school">High School Graduate</option>
              <option value="senior_high">Senior High School Graduate</option>
              <option value="bachelors">Bachelor's Degree</option>
              <option value="masters">Master's Degree</option>
              <option value="doctorate">Doctorate/PhD</option>
            </select>
          </div>
        </div>

        <!-- Career -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Career</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Job Title</label>
              <input v-model="form.current_job_title" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
              <input v-model="form.industry" type="text" placeholder="e.g., Technology, Finance, Healthcare" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
              <input v-model="form.city" type="text" placeholder="e.g., Manila" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
              <input v-model="form.country" type="text" placeholder="e.g., Philippines" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Employment Status</label>
              <select v-model="form.employment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Select status</option>
                <option value="employed_full_time">Employed (Full-Time)</option>
                <option value="employed_part_time">Employed (Part-Time)</option>
                <option value="self_employed">Self-Employed / Freelance</option>
                <option value="in_study">In Further Study (e.g., Master's)</option>
                <option value="unemployed_looking">Unemployed (Actively Looking)</option>
                <option value="unemployed_not_looking">Unemployed (Not Looking)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Employment Sector <span class="text-gray-400 text-xs">(Optional)</span></label>
              <div class="flex gap-4">
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.employment_sector" value="public_government" class="border-gray-300" />
                  <span class="text-sm">Government/Public</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.employment_sector" value="private" class="border-gray-300" />
                  <span class="text-sm">Private</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="radio" v-model="form.employment_sector" value="ngo_nonprofit" class="border-gray-300" />
                  <span class="text-sm">NGO/Non-Profit</span>
                </label>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
              <input v-model.number="form.years_of_experience" type="number" min="0" max="100" placeholder="Enter years" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary Range (PHP)</label>
              <input v-model="form.salary_range" type="text" placeholder="e.g., 40,000 - 60,000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="md:col-span-2">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="form.is_lcba_employee_faculty" class="rounded border-gray-300" />
                <span class="text-sm font-medium text-gray-700">I am a current/former LCBA Employee or Faculty</span>
              </label>
              
              <div v-if="form.is_lcba_employee_faculty" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="mb-3">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Employee/Faculty ID</label>
                  <input v-model="form.lcba_employee_id" type="text" placeholder="Enter your LCBA ID" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                  <p class="text-xs text-gray-600 mt-1">This will be verified by administrators</p>
                </div>
                
                <div v-if="form.lcba_verification_status" class="flex items-center gap-2">
                  <span v-if="form.lcba_verification_status === 'verified'" class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Verified LCBA Employee/Faculty
                  </span>
                  <span v-else-if="form.lcba_verification_status === 'pending'" class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                    ⏳ Verification Pending
                  </span>
                  <span v-else-if="form.lcba_verification_status === 'rejected'" class="inline-flex items-center gap-1 bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                    ❌ Verification Rejected
                  </span>
                </div>
              </div>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
              <!-- Tag Input for Skills -->
              <div class="border border-gray-300 rounded-lg p-2 min-h-[42px] focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent">
                <div class="flex flex-wrap gap-2 mb-2">
                  <span
                    v-for="(skill, index) in form.skills"
                    :key="index"
                    class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm"
                  >
                    {{ skill }}
                    <button
                      @click="removeSkill(index)"
                      class="text-blue-600 hover:text-blue-800 focus:outline-none"
                      type="button"
                    >
                      ×
                    </button>
                  </span>
                </div>
                <input
                  v-model="skillInput"
                  @keydown.enter.prevent="addSkill"
                  @input="searchSkills"
                  type="text"
                  placeholder="Type and press Enter to add skill"
                  class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none"
                />
                <!-- Autocomplete Dropdown -->
                <div v-if="skillSuggestions.length > 0 && skillInput" class="mt-2 border border-gray-200 rounded-lg bg-white shadow-lg max-h-48 overflow-y-auto">
                  <button
                    v-for="suggestion in skillSuggestions"
                    :key="suggestion.id"
                    @click="selectSkill(suggestion.name)"
                    type="button"
                    class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center justify-between"
                  >
                    <span>{{ suggestion.name }}</span>
                    <span class="text-xs text-gray-500">{{ suggestion.category }}</span>
                  </button>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Skills are standardized for better matching</p>
            </div>
          </div>
        </div>

        <!-- Career Preferences -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Career Preferences</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Work Setup</label>
              <div class="flex flex-wrap gap-3">
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.work_setup_preferences" value="on_site" class="rounded border-gray-300" />
                  <span class="text-sm">On-Site</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.work_setup_preferences" value="hybrid" class="rounded border-gray-300" />
                  <span class="text-sm">Hybrid</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.work_setup_preferences" value="remote" class="rounded border-gray-300" />
                  <span class="text-sm">Remote</span>
                </label>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
              <div class="flex flex-wrap gap-3">
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.employment_type_preferences" value="full_time" class="rounded border-gray-300" />
                  <span class="text-sm">Full-Time</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.employment_type_preferences" value="part_time" class="rounded border-gray-300" />
                  <span class="text-sm">Part-Time</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.employment_type_preferences" value="contract" class="rounded border-gray-300" />
                  <span class="text-sm">Contract</span>
                </label>
                <label class="flex items-center gap-2">
                  <input type="checkbox" v-model="form.employment_type_preferences" value="internship" class="rounded border-gray-300" />
                  <span class="text-sm">Internship</span>
                </label>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Industries of Interest</label>
              <!-- Tag Input for Industries -->
              <div class="border border-gray-300 rounded-lg p-2 min-h-[42px] focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent">
                <div class="flex flex-wrap gap-2 mb-2">
                  <span
                    v-for="(industry, index) in form.industries_of_interest"
                    :key="index"
                    class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm"
                  >
                    {{ industry }}
                    <button
                      @click="removeIndustry(index)"
                      class="text-green-600 hover:text-green-800 focus:outline-none"
                      type="button"
                    >
                      ×
                    </button>
                  </span>
                </div>
                <input
                  v-model="industryInput"
                  @keydown.enter.prevent="addIndustry"
                  type="text"
                  placeholder="Type and press Enter to add industry"
                  class="w-full px-2 py-1 border-0 focus:ring-0 focus:outline-none"
                />
              </div>
              <p class="text-xs text-gray-500 mt-1">List industries you're interested in working in</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Privacy Settings -->
      <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <span class="mr-2">🔒</span>Privacy Settings
          </h2>
          <p class="text-sm text-gray-600 mb-4">Control who can see your profile information in compliance with the Data Privacy Act.</p>
          
          <div class="space-y-4">
            <!-- Email Visibility -->
            <div class="border-b border-gray-200 pb-3">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-800">Email Address</span>
                <select v-model="privacySettings.email" class="px-3 py-1 border border-gray-300 rounded text-sm">
                  <option value="public">Public</option>
                  <option value="alumni_only">Alumni Only</option>
                  <option value="connections_only">Connections Only</option>
                  <option value="admin_only">Private (Admin Only)</option>
                </select>
              </div>
              <p class="text-xs text-gray-500">Your login email: {{ form.email }}</p>
            </div>

            <!-- Contact Information -->
            <div class="border-b border-gray-200 pb-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-800">Phone Number & Address</span>
                <select v-model="privacySettings.contact" class="px-3 py-1 border border-gray-300 rounded text-sm">
                  <option value="public">Public</option>
                  <option value="alumni_only">Alumni Only</option>
                  <option value="connections_only">Connections Only</option>
                  <option value="admin_only">Private (Admin Only)</option>
                </select>
              </div>
            </div>

            <!-- Birthday -->
            <div class="border-b border-gray-200 pb-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-800">Birthday</span>
                <select v-model="privacySettings.birthdate" class="px-3 py-1 border border-gray-300 rounded text-sm">
                  <option value="public">Public</option>
                  <option value="alumni_only">Alumni Only</option>
                  <option value="connections_only">Connections Only</option>
                  <option value="admin_only">Private (Admin Only)</option>
                </select>
              </div>
              <p class="text-xs text-gray-500">Affects Birthday Corner visibility</p>
            </div>

            <!-- Education History -->
            <div class="border-b border-gray-200 pb-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-800">Education History</span>
                <select v-model="privacySettings.education" class="px-3 py-1 border border-gray-300 rounded text-sm">
                  <option value="public">Public</option>
                  <option value="alumni_only">Alumni Only</option>
                  <option value="connections_only">Connections Only</option>
                  <option value="admin_only">Private (Admin Only)</option>
                </select>
              </div>
            </div>

            <!-- Career Information -->
            <div class="border-b border-gray-200 pb-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-800">Current Job & Salary</span>
                <select v-model="privacySettings.career" class="px-3 py-1 border border-gray-300 rounded text-sm">
                  <option value="public">Public</option>
                  <option value="alumni_only">Alumni Only</option>
                  <option value="connections_only">Connections Only</option>
                  <option value="admin_only">Private (Admin Only)</option>
                </select>
              </div>
            </div>

            <!-- Location -->
            <div class="pb-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-800">Location (City & Country)</span>
                <select v-model="privacySettings.location" class="px-3 py-1 border border-gray-300 rounded text-sm">
                  <option value="public">Public</option>
                  <option value="alumni_only">Alumni Only</option>
                  <option value="connections_only">Connections Only</option>
                  <option value="admin_only">Private (Admin Only)</option>
                </select>
              </div>
            </div>
          </div>

          <div class="mt-4 p-3 bg-blue-50 border-l-4 border-blue-400 text-sm">
            <p class="text-gray-700">
              <strong>Note:</strong> Administrators can always view your information for verification and compliance purposes. Your basic information (name, LCBA program) is always visible to alumni.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Education Modal -->
  <div v-if="showEducationModal" class="fixed inset-0 bg-transparent flex items-center justify-center z-50" @click.self="closeEducationModal">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900">
          {{ editingEducation ? 'Edit Education' : 'Add Education' }}
        </h2>
        <button @click="closeEducationModal" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <form @submit.prevent="saveEducation">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Education Level <span class="text-red-500">*</span></label>
            <select v-model="educationForm.level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="">Select level</option>
              <option value="elementary">Elementary</option>
              <option value="high_school">High School</option>
              <option value="senior_high">Senior High School</option>
              <option value="college">College/Bachelor's</option>
              <option value="masters">Master's Degree</option>
              <option value="doctorate">Doctorate/PhD</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">School Name <span class="text-red-500">*</span></label>
            <input v-model="educationForm.school_name" required type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., Laguna College of Business and Arts">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Year Graduated</label>
            <input v-model="educationForm.year_graduated" type="number" min="1950" :max="new Date().getFullYear() + 10" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2020">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Awards/Honors</label>
            <textarea v-model="educationForm.awards" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none" placeholder="e.g., Cum Laude, Dean's Lister"></textarea>
          </div>

          <div class="flex items-center">
            <input v-model="educationForm.is_lcba" type="checkbox" class="rounded border-gray-300 text-blue-600">
            <label class="ml-2 text-sm text-gray-700">This is from LCBA</label>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button type="button" @click="closeEducationModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit" :disabled="submittingEducation" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            {{ submittingEducation ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../config/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const saving = ref(false)
const skillInput = ref('')
const industryInput = ref('')
const skillSuggestions = ref([])
let skillSearchTimeout = null

// Education History
const educationHistory = ref([])
const showEducationModal = ref(false)
const editingEducation = ref(null)
const submittingEducation = ref(false)
const educationForm = ref({
  level: '',
  school_name: '',
  year_graduated: '',
  awards: '',
  is_lcba: false
})

const form = ref({
  first_name: '',
  middle_name: '',
  last_name: '',
  suffix: '',
  birthdate: '',
  email: '',
  headline: '',
  // Contact & Socials
  linkedin_url: '',
  portfolio_url: '',
  // Education
  program: '',
  batch: '',
  highest_educational_attainment: '',
  // Career
  current_job_title: '',
  industry: '',
  city: '',
  country: '',
  employment_status: '',
  employment_sector: '',
  years_of_experience: '',
  salary_range: '',
  skills: [],
  // Career Preferences
  work_setup_preferences: [],
  employment_type_preferences: [],
  industries_of_interest: [],
  // LCBA Employee/Faculty
  is_lcba_employee_faculty: false,
  lcba_employee_id: '',
  lcba_verification_status: ''
})

const privacySettings = ref({
  email: 'alumni_only',
  contact: 'alumni_only',
  birthdate: 'alumni_only',
  education: 'alumni_only',
  career: 'alumni_only',
  location: 'public'
})

const loadProfile = async () => {
  try {
    const me = authStore.user
    if (me) {
      form.value = {
        first_name: me.first_name || '',
        middle_name: me.middle_name || '',
        last_name: me.last_name || '',
        suffix: me.suffix || '',
        birthdate: me.birthdate || '',
        email: me.email || '',
        headline: me.headline || '',
        linkedin_url: me.linkedin_url || '',
        portfolio_url: me.portfolio_url || '',
        program: me.program || '',
        batch: me.batch || '',
        highest_educational_attainment: me.highest_educational_attainment || '',
        current_job_title: me.current_job_title || '',
        industry: me.industry || '',
        city: me.city || '',
        country: me.country || '',
        employment_status: me.employment_status || '',
        employment_sector: me.employment_sector || '',
        years_of_experience: me.years_of_experience || '',
        salary_range: me.salary_range || '',
        skills: me.skills || [],
        work_setup_preferences: me.work_setup_preferences || [],
        employment_type_preferences: me.employment_type_preferences || [],
        industries_of_interest: me.industries_of_interest || [],
        is_lcba_employee_faculty: me.is_lcba_employee_faculty || false,
        lcba_employee_id: me.lcba_employee_id || '',
        lcba_verification_status: me.lcba_verification_status || ''
      }
      if (me.privacy_settings) {
        privacySettings.value = { ...privacySettings.value, ...me.privacy_settings }
      }
    }
  } catch (error) {
    console.error('Error loading profile:', error)
  }
}

const searchSkills = async () => {
  if (!skillInput.value || skillInput.value.length < 2) {
    skillSuggestions.value = []
    return
  }

  clearTimeout(skillSearchTimeout)
  skillSearchTimeout = setTimeout(async () => {
    try {
      const response = await axios.get('/api/skills', {
        params: { q: skillInput.value, limit: 10 }
      })
      if (response.data.success) {
        skillSuggestions.value = response.data.data.filter(
          skill => !form.value.skills.includes(skill.name)
        )
      }
    } catch (error) {
      console.error('Error searching skills:', error)
    }
  }, 300)
}

const addSkill = () => {
  const skill = skillInput.value.trim()
  if (skill && !form.value.skills.includes(skill)) {
    form.value.skills.push(skill)
    skillInput.value = ''
    skillSuggestions.value = []
  }
}

const selectSkill = (skillName) => {
  if (!form.value.skills.includes(skillName)) {
    form.value.skills.push(skillName)
  }
  skillInput.value = ''
  skillSuggestions.value = []
}

const removeSkill = (index) => {
  form.value.skills.splice(index, 1)
}

const addIndustry = () => {
  const industry = industryInput.value.trim()
  if (industry && !form.value.industries_of_interest.includes(industry)) {
    form.value.industries_of_interest.push(industry)
    industryInput.value = ''
  }
}

const removeIndustry = (index) => {
  form.value.industries_of_interest.splice(index, 1)
}

const saveProfile = async () => {
  saving.value = true
  try {
    const payload = {
      ...form.value,
      privacy_settings: privacySettings.value
    }
    const res = await axios.put('/api/user/profile', payload)
    if (res?.data) {
      await authStore.checkAuth()
      alert('Profile updated successfully')
    }
  } catch (e) {
    console.error('Save profile failed:', e)
    alert('Failed to save profile: ' + (e.response?.data?.message || e.message))
  } finally {
    saving.value = false
  }
}

const loadEducationHistory = async () => {
  try {
    const response = await axios.get('/api/user/education')
    if (response?.data?.success) {
      educationHistory.value = response.data.data
    }
  } catch (error) {
    console.error('Error loading education history:', error)
  }
}

const getLevelLabel = (level) => {
  const labels = {
    elementary: 'Elementary',
    high_school: 'High School',
    senior_high: 'Senior High School',
    college: 'College',
    masters: "Master's Degree",
    doctorate: 'Doctorate'
  }
  return labels[level] || level
}

const editEducation = (edu) => {
  editingEducation.value = edu
  educationForm.value = {
    level: edu.level,
    school_name: edu.school_name,
    year_graduated: edu.year_graduated || '',
    awards: edu.awards || '',
    is_lcba: edu.is_lcba || false
  }
  showEducationModal.value = true
}

const closeEducationModal = () => {
  showEducationModal.value = false
  editingEducation.value = null
  educationForm.value = {
    level: '',
    school_name: '',
    year_graduated: '',
    awards: '',
    is_lcba: false
  }
}

const saveEducation = async () => {
  submittingEducation.value = true
  try {
    const payload = {
      level: educationForm.value.level,
      school_name: educationForm.value.school_name,
      year_graduated: educationForm.value.year_graduated || null,
      awards: educationForm.value.awards || null,
      is_lcba: educationForm.value.is_lcba
    }

    if (editingEducation.value) {
      // Update existing
      const response = await axios.put(`/api/user/education/${editingEducation.value.id}`, payload)
      if (response?.data?.success) {
        await loadEducationHistory()
        closeEducationModal()
        alert('Education updated successfully')
      }
    } else {
      // Create new
      const response = await axios.post('/api/user/education', payload)
      if (response?.data?.success) {
        await loadEducationHistory()
        closeEducationModal()
        alert('Education added successfully')
      }
    }
  } catch (error) {
    console.error('Error saving education:', error)
    alert('Failed to save education: ' + (error.response?.data?.message || error.message))
  } finally {
    submittingEducation.value = false
  }
}

const deleteEducation = async (id) => {
  if (!confirm('Are you sure you want to delete this education entry?')) return

  try {
    const response = await axios.delete(`/api/user/education/${id}`)
    if (response?.data?.success) {
      await loadEducationHistory()
      alert('Education deleted successfully')
    }
  } catch (error) {
    console.error('Error deleting education:', error)
    alert('Failed to delete education')
  }
}

onMounted(() => {
  loadProfile()
  loadEducationHistory()
})
</script>
