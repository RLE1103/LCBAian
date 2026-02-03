<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8">
      <!-- Icon -->
      <div class="flex justify-center mb-6">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
          <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
      </div>

      <!-- Title -->
      <h1 class="text-2xl font-bold text-gray-900 text-center mb-2">
        Account Pending Verification
      </h1>

      <!-- Message -->
      <p class="text-gray-600 text-center mb-6">
        Thank you for registering with <span class="font-semibold text-blue-600">LCBAConnect+</span>. 
        Your account is currently being reviewed by our administrators.
      </p>

      <!-- Info Box -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
          <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-900 mb-1">What happens next?</p>
            <ul class="text-sm text-blue-800 space-y-1">
              <li>• Our team will review your registration</li>
              <li>• You'll receive an email once verified</li>
              <li>• Verification typically takes 24-48 hours</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Check Status Button -->
      <button
        @click="checkStatus"
        :disabled="checking"
        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium mb-4"
      >
        <span v-if="!checking">Check Verification Status</span>
        <span v-else class="flex items-center justify-center">
          <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Checking...
        </span>
      </button>

      <!-- Logout Button -->
      <button
        @click="logout"
        class="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium mb-4"
      >
        Sign Out
      </button>

      <!-- Contact Support -->
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Need help? 
          <a href="mailto:admin@lcbaconnect.edu.ph" class="text-blue-600 hover:text-blue-800 font-medium">
            Contact Support
          </a>
        </p>
      </div>

      <!-- Last Checked -->
      <p v-if="lastChecked" class="text-xs text-gray-500 text-center mt-4">
        Last checked: {{ formatTime(lastChecked) }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useToast } from '../composables/useToast'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const checking = ref(false)
const lastChecked = ref(null)

const checkStatus = async () => {
  checking.value = true
  try {
    await authStore.checkAuth()
    lastChecked.value = new Date()
    
    if (authStore.user?.is_verified) {
      toast.success('Your account has been verified! Redirecting...', 'Success')
      setTimeout(() => {
        router.push('/dashboard')
      }, 1500)
    } else {
      toast.info('Your account is still pending verification. Please check back later.', 'Pending')
    }
  } catch (error) {
    console.error('Error checking status:', error)
    toast.error('Failed to check status. Please try again.', 'Error')
  } finally {
    checking.value = false
  }
}

const logout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Error logging out:', error)
  }
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
