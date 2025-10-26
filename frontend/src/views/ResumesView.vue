<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">My Resumes</h1>
        <p class="text-gray-600 mt-1">Manage and customize your professional resumes</p>
      </div>
      <div class="flex items-center space-x-4">
        <button 
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Create Resume
        </button>
        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
          </svg>
          Upload Resume
            </button>
          </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="relative">
            <input 
          v-model="searchQuery"
              type="text" 
              placeholder="Search by resume title or tags..." 
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - Resume List -->
      <div class="lg:col-span-2">
        <!-- View Toggle -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-4">
            <button 
              @click="viewMode = 'table'"
              :class="[
                'px-3 py-1 rounded-lg text-sm font-medium',
                viewMode === 'table' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-800'
              ]"
            >
              Table View
            </button>
            <button 
              @click="viewMode = 'cards'"
              :class="[
                'px-3 py-1 rounded-lg text-sm font-medium',
                viewMode === 'cards' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-800'
              ]"
            >
              Card View
            </button>
          </div>
          <div class="text-sm text-gray-600">
            {{ filteredResumes.length }} resumes
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredResumes.length === 0" class="text-center py-12 text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p>No resumes found</p>
          <p class="text-sm">Create your first resume to get started</p>
          <button 
            @click="showCreateModal = true"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
          >
        Create Resume
      </button>
    </div>

        <!-- Table View -->
        <div v-else-if="viewMode === 'table'" class="bg-white rounded-lg shadow-md overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resume Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visibility</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="resume in filteredResumes" 
                :key="resume.id"
                class="hover:bg-gray-50 cursor-pointer"
                @click="viewResume(resume)"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ resume.title }}</div>
                  <div class="text-sm text-gray-500">{{ resume.tags.join(', ') }}</div>
                  </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(resume.updated_at) }}
                  </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    resume.visibility === 'public' ? 'bg-green-100 text-green-800' :
                    resume.visibility === 'private' ? 'bg-gray-100 text-gray-800' :
                    'bg-blue-100 text-blue-800'
                  ]">
                    {{ getVisibilityLabel(resume.visibility) }}
                  </span>
                  </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-2">
                    <button 
                      @click.stop="previewResume(resume)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Preview
                      </button>
                    <button 
                      @click.stop="editResume(resume)"
                      class="text-gray-600 hover:text-gray-900"
                    >
                      Edit
                      </button>
                    <button 
                      @click.stop="downloadResume(resume)"
                      class="text-gray-600 hover:text-gray-900"
                    >
                      Download
                      </button>
                    <button 
                      @click.stop="shareResume(resume)"
                      class="text-gray-600 hover:text-gray-900"
                    >
                      Share
                      </button>
                    <button 
                      @click.stop="deleteResume(resume)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
        </div>

        <!-- Card View -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="resume in filteredResumes" 
            :key="resume.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
            @click="viewResume(resume)"
          >
            <div class="flex items-start justify-between mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ resume.title }}</h3>
                <p class="text-sm text-gray-600">Last updated: {{ formatDate(resume.updated_at) }}</p>
              </div>
              <span :class="[
                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                resume.visibility === 'public' ? 'bg-green-100 text-green-800' :
                resume.visibility === 'private' ? 'bg-gray-100 text-gray-800' :
                'bg-blue-100 text-blue-800'
              ]">
                {{ getVisibilityLabel(resume.visibility) }}
              </span>
            </div>
            
            <div class="mb-4">
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="tag in resume.tags" 
                  :key="tag"
                  class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                >
                  {{ tag }}
                </span>
              </div>
            </div>
            
            <div class="flex space-x-2">
              <button 
                @click.stop="previewResume(resume)"
                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700"
              >
                Preview
              </button>
              <button 
                @click.stop="editResume(resume)"
                class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50"
              >
                Edit
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Quick Panels -->
      <div class="space-y-6">
        <!-- Tips for Strong Resume -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Tips for a Strong Resume</h3>
          <div class="space-y-3 text-sm text-gray-700">
            <div class="flex items-start">
              <span class="text-blue-500 mr-2">•</span>
              <span>Use action verbs to describe your achievements</span>
            </div>
            <div class="flex items-start">
              <span class="text-blue-500 mr-2">•</span>
              <span>Quantify your accomplishments with numbers</span>
            </div>
            <div class="flex items-start">
              <span class="text-blue-500 mr-2">•</span>
              <span>Tailor your resume to each job application</span>
            </div>
            <div class="flex items-start">
              <span class="text-blue-500 mr-2">•</span>
              <span>Keep it concise and easy to scan</span>
            </div>
            <div class="flex items-start">
              <span class="text-blue-500 mr-2">•</span>
              <span>Include relevant keywords from job descriptions</span>
            </div>
          </div>
        </div>
          
          <!-- Popular Templates -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Templates</h3>
          <div class="space-y-3">
            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
              <h4 class="font-medium text-gray-900">Minimal</h4>
              <p class="text-sm text-gray-600">Clean and simple design</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
              <h4 class="font-medium text-gray-900">Professional</h4>
              <p class="text-sm text-gray-600">Traditional corporate style</p>
            </div>
            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer">
              <h4 class="font-medium text-gray-900">Modern</h4>
              <p class="text-sm text-gray-600">Contemporary and creative</p>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
            <div class="space-y-2">
            <button class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
              <span class="mr-2">🎯</span>
              Tailor Resume to a Job
            </button>
            <button class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
              <span class="mr-2">🔗</span>
              Import from LinkedIn
            </button>
            <button class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
              <span class="mr-2">📊</span>
              Resume Analytics
            </button>
            <button class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg flex items-center">
              <span class="mr-2">💡</span>
              AI Writing Assistant
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create Resume Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 w-full max-w-md border-4 border-gray-300 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Create New Resume</h3>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Resume Title</label>
          <input 
            v-model="newResumeTitle"
            type="text"
            placeholder="e.g., Software Engineer Resume"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
          <select v-model="selectedTemplate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="minimal">Minimal</option>
            <option value="professional">Professional</option>
            <option value="modern">Modern</option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
          <select v-model="newResumeVisibility" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="private">Private</option>
            <option value="public">Public to Alumni</option>
            <option value="link">Link Only</option>
          </select>
        </div>
        
        <div class="flex justify-end space-x-2">
          <button @click="showCreateModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="createResume" :disabled="!newResumeTitle.trim()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            Create Resume
          </button>
        </div>
              </div>
              </div>

    <!-- Resume Editor Modal -->
    <div v-if="showEditorModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg w-full max-w-6xl max-h-[90vh] overflow-y-auto border-4 border-gray-300 shadow-2xl">
        <div class="p-6">
          <!-- Editor Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">{{ editingResume?.title || 'Resume Editor' }}</h2>
              <p class="text-gray-600">Edit your resume sections</p>
              </div>
            <div class="flex items-center space-x-2">
              <button class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Save Draft
              </button>
              <button @click="showEditorModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Editor -->
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Resume Sections</h3>
              <div class="space-y-4">
                <!-- Summary Section -->
                <div class="border border-gray-200 rounded-lg p-4">
                  <h4 class="font-medium text-gray-900 mb-2">Summary / Objective</h4>
                  <textarea 
                    v-model="resumeData.summary"
                    rows="3"
                    placeholder="Write a brief summary of your professional background..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  ></textarea>
                </div>

                <!-- Work Experience Section -->
                <div class="border border-gray-200 rounded-lg p-4">
                  <h4 class="font-medium text-gray-900 mb-2">Work Experience</h4>
                  <div class="space-y-3">
                    <div v-for="(experience, index) in resumeData.experience" :key="index" class="border border-gray-200 rounded-lg p-3">
                      <input 
                        v-model="experience.title"
                        type="text"
                        placeholder="Job Title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                      <input 
                        v-model="experience.company"
                        type="text"
                        placeholder="Company"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                      <input 
                        v-model="experience.duration"
                        type="text"
                        placeholder="Duration (e.g., Jan 2020 - Present)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                      <textarea 
                        v-model="experience.description"
                        rows="2"
                        placeholder="Job responsibilities and achievements..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      ></textarea>
                    </div>
                    <button @click="addExperience" class="text-blue-600 hover:text-blue-800 text-sm">
                      + Add Experience
                    </button>
                  </div>
                </div>

                <!-- Education Section -->
                <div class="border border-gray-200 rounded-lg p-4">
                  <h4 class="font-medium text-gray-900 mb-2">Education</h4>
                  <div class="space-y-3">
                    <div v-for="(education, index) in resumeData.education" :key="index" class="border border-gray-200 rounded-lg p-3">
                      <input 
                        v-model="education.degree"
                        type="text"
                        placeholder="Degree (e.g., Bachelor of Science in Computer Science)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                      <input 
                        v-model="education.school"
                        type="text"
                        placeholder="School/University"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                      <input 
                        v-model="education.year"
                        type="text"
                        placeholder="Graduation Year"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      />
                    </div>
                    <button @click="addEducation" class="text-blue-600 hover:text-blue-800 text-sm">
                      + Add Education
                    </button>
                  </div>
                </div>

                <!-- Skills Section -->
                <div class="border border-gray-200 rounded-lg p-4">
                  <h4 class="font-medium text-gray-900 mb-2">Skills</h4>
                  <div class="flex flex-wrap gap-2 mb-3">
                    <span 
                      v-for="(skill, index) in resumeData.skills" 
                      :key="index"
                      class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full flex items-center"
                    >
                      {{ skill }}
                      <button @click="removeSkill(index)" class="ml-2 text-blue-600 hover:text-blue-800">
                        ×
                      </button>
                    </span>
                  </div>
                  <div class="flex">
                    <input 
                      v-model="newSkill"
                      @keypress.enter="addSkill"
                      type="text"
                      placeholder="Add a skill..."
                      class="flex-1 px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                    <button @click="addSkill" class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                      Add
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column - Live Preview -->
          <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Live Preview</h3>
              <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                <div class="bg-white p-6 shadow-sm">
                  <!-- Resume Preview Content -->
                  <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ resumeData.name || 'Your Name' }}</h1>
                    <p class="text-gray-600">{{ resumeData.email || 'your.email@example.com' }}</p>
                    <p class="text-gray-600">{{ resumeData.phone || '(555) 123-4567' }}</p>
                  </div>

                  <div v-if="resumeData.summary" class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Summary</h2>
                    <p class="text-gray-700">{{ resumeData.summary }}</p>
                  </div>

                  <div v-if="resumeData.experience.length > 0" class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Work Experience</h2>
                    <div v-for="(experience, index) in resumeData.experience" :key="index" class="mb-4">
                      <h3 class="font-medium text-gray-900">{{ experience.title }}</h3>
                      <p class="text-gray-600">{{ experience.company }} • {{ experience.duration }}</p>
                      <p class="text-gray-700 text-sm">{{ experience.description }}</p>
                    </div>
                  </div>

                  <div v-if="resumeData.education.length > 0" class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Education</h2>
                    <div v-for="(education, index) in resumeData.education" :key="index" class="mb-4">
                      <h3 class="font-medium text-gray-900">{{ education.degree }}</h3>
                      <p class="text-gray-600">{{ education.school }} • {{ education.year }}</p>
                    </div>
                  </div>

                  <div v-if="resumeData.skills.length > 0" class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Skills</h2>
                    <div class="flex flex-wrap gap-2">
                      <span 
                        v-for="skill in resumeData.skills" 
                        :key="skill"
                        class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full"
                      >
                        {{ skill }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Export Options -->
              <div class="mt-4 flex space-x-2">
                <button class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                  Export PDF
                </button>
                <button class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50">
                  Export DOCX
                </button>
                <button class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50">
                  Share Link
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const searchQuery = ref('')
const viewMode = ref('table')
const showCreateModal = ref(false)
const showEditorModal = ref(false)
const newResumeTitle = ref('')
const selectedTemplate = ref('minimal')
const newResumeVisibility = ref('private')
const editingResume = ref(null)
const newSkill = ref('')

// Resume data for editor
const resumeData = ref({
  name: 'John Doe',
  email: 'john.doe@example.com',
  phone: '(555) 123-4567',
  summary: '',
  experience: [
    {
      title: '',
      company: '',
      duration: '',
      description: ''
    }
  ],
  education: [
    {
      degree: '',
      school: '',
      year: ''
    }
  ],
  skills: ['JavaScript', 'React', 'Node.js', 'Python']
})

// Sample data - replace with API calls
const resumes = ref([
  {
    id: 1,
    title: 'Software Engineer Resume',
    tags: ['Software', 'Engineering', 'Full-stack'],
    visibility: 'public',
    updated_at: new Date('2025-02-05'),
    template: 'professional'
  },
  {
    id: 2,
    title: 'Internship Resume',
    tags: ['Internship', 'Entry-level', 'Student'],
    visibility: 'private',
    updated_at: new Date('2025-01-20'),
    template: 'minimal'
  },
  {
    id: 3,
    title: 'Senior Developer Resume',
    tags: ['Senior', 'Leadership', 'Architecture'],
    visibility: 'link',
    updated_at: new Date('2025-01-15'),
    template: 'modern'
  }
])

// Computed properties
const filteredResumes = computed(() => {
  if (!searchQuery.value) return resumes.value
  
  return resumes.value.filter(resume => 
    resume.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    resume.tags.some(tag => tag.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

// Methods
const getVisibilityLabel = (visibility) => {
  switch (visibility) {
    case 'public': return '🌐 Public to Alumni'
    case 'private': return '🔒 Private'
    case 'link': return '🔗 Link Only'
    default: return visibility
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const viewResume = (resume) => {
  console.log('View resume:', resume.title)
}

const previewResume = (resume) => {
  console.log('Preview resume:', resume.title)
}

const editResume = (resume) => {
  editingResume.value = resume
  showEditorModal.value = true
}

const downloadResume = (resume) => {
  console.log('Download resume:', resume.title)
}

const shareResume = (resume) => {
  console.log('Share resume:', resume.title)
}

const deleteResume = (resume) => {
  if (confirm(`Are you sure you want to delete "${resume.title}"?`)) {
    const index = resumes.value.findIndex(r => r.id === resume.id)
    if (index > -1) {
      resumes.value.splice(index, 1)
    }
  }
}

const createResume = () => {
  if (!newResumeTitle.value.trim()) return

  const newResume = {
    id: Date.now(),
    title: newResumeTitle.value,
    tags: [],
    visibility: newResumeVisibility.value,
    updated_at: new Date(),
    template: selectedTemplate.value
  }

  resumes.value.unshift(newResume)
  
  // Reset form
  showCreateModal.value = false
  newResumeTitle.value = ''
  selectedTemplate.value = 'minimal'
  newResumeVisibility.value = 'private'
}

const addExperience = () => {
  resumeData.value.experience.push({
    title: '',
    company: '',
    duration: '',
    description: ''
  })
}

const addEducation = () => {
  resumeData.value.education.push({
    degree: '',
    school: '',
    year: ''
  })
}

const addSkill = () => {
  if (newSkill.value.trim() && !resumeData.value.skills.includes(newSkill.value.trim())) {
    resumeData.value.skills.push(newSkill.value.trim())
    newSkill.value = ''
  }
}

const removeSkill = (index) => {
  resumeData.value.skills.splice(index, 1)
}

onMounted(() => {
  // Load initial data
})
</script>