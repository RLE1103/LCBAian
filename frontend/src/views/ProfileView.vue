<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
        <p class="text-gray-600 mt-1">Manage your information and privacy settings</p>
      </div>
      <div class="flex items-center gap-2">
        <button @click="saveProfile" :disabled="saving" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50">
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
              <input v-model="form.first_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
              <input v-model="form.last_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Headline</label>
              <input v-model="form.headline" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Education</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
              <input v-model="form.program" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., BS Computer Science" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Batch (Graduation Year)</label>
              <input v-model="form.batch" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Career</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Job Title</label>
              <input v-model="form.current_job_title" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
              <input v-model="form.industry" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
              <input v-model="form.location" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Skills (comma separated)</label>
              <input v-model="skillsInput" type="text" placeholder="e.g. Vue, Laravel, MySQL" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Visibility Settings</h2>
          <p class="text-sm text-gray-600 mb-4">Control who can see each section of your profile.</p>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-800">Basic Information</span>
              <select v-model="visibility.basic" class="px-2 py-1 border border-gray-300 rounded">
                <option value="public">Public</option>
                <option value="alumni">Alumni Only</option>
                <option value="private">Private</option>
              </select>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-800">Education</span>
              <select v-model="visibility.education" class="px-2 py-1 border border-gray-300 rounded">
                <option value="public">Public</option>
                <option value="alumni">Alumni Only</option>
                <option value="private">Private</option>
              </select>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-800">Career</span>
              <select v-model="visibility.career" class="px-2 py-1 border border-gray-300 rounded">
                <option value="public">Public</option>
                <option value="alumni">Alumni Only</option>
                <option value="private">Private</option>
              </select>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-4">Admin can override default visibility for compliance reasons.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../config/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const saving = ref(false)

const form = ref({
  first_name: '', last_name: '', headline: '', program: '', batch: '', current_job_title: '', industry: '', location: '', skills: []
})
const skillsInput = ref('')
const visibility = ref({ basic: 'public', education: 'alumni', career: 'alumni' })

const loadProfile = async () => {
  try {
    const me = authStore.user
    if (me) {
      form.value = {
        first_name: me.first_name || '',
        last_name: me.last_name || '',
        headline: me.headline || '',
        program: me.program || '',
        batch: me.batch || '',
        current_job_title: me.current_job_title || '',
        industry: me.industry || '',
        location: me.location || '',
        skills: me.skills || []
      }
      skillsInput.value = (form.value.skills || []).join(', ')
    }
  } catch {}
}

const saveProfile = async () => {
  saving.value = true
  try {
    form.value.skills = skillsInput.value ? skillsInput.value.split(',').map(s => s.trim()).filter(Boolean) : []
    const payload = { ...form.value, visibility_settings: visibility.value }
    const res = await axios.put('/api/user/profile', payload)
    if (res?.data) {
      await authStore.checkAuth()
      alert('Profile updated successfully')
    }
  } catch (e) {
    console.error('Save profile failed:', e)
    alert('Failed to save profile')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadProfile()
})
</script>


