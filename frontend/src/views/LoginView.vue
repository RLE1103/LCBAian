<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-gray-300">
      <!-- Logo and Header -->
      <div class="text-center mb-8">
        <div class="flex items-center justify-center mb-4">
          <img src="/src/assets/images/LCBAlogo.png" alt="LCBA Logo" class="w-30 h-30 rounded object-cover" />
        </div>
        <h1 class="text-3xl font-bold text-gray-900">LCBAConnect+</h1>
        <p class="text-gray-600 mt-2">Alumni Network Platform</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="Enter your email"
            :class="{ 'border-red-500': errors.email }"
          />
          <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <div class="relative">
            <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors pr-12"
              placeholder="Enter your password"
              :class="{ 'border-red-500': errors.password }"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
              </svg>
            </button>
          </div>
          <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input
              v-model="form.remember"
              type="checkbox"
              class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
          <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Forgot password?</a>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Signing in...
          </span>
          <span v-else>Sign In</span>
        </button>
      </form>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex">
          <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="ml-3">
            <p class="text-sm text-red-800">{{ errorMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Register Link -->
      <div class="mt-6 text-center">
        <p class="text-gray-600">
          Don't have an account?
          <router-link to="/register" class="text-blue-600 hover:text-blue-800 font-medium">
            Create one here
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
})

// UI state
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const errors = reactive({})

// Validation
const validateForm = () => {
  errors.email = ''
  errors.password = ''
  
  if (!form.email) {
    errors.email = 'Email is required'
    return false
  }
  
  if (!form.email.includes('@')) {
    errors.email = 'Please enter a valid email address'
    return false
  }
  
  if (!form.password) {
    errors.password = 'Password is required'
    return false
  }
  
  if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
    return false
  }
  
  return true
}

// Login handler
const handleLogin = async () => {
  if (!validateForm()) return
  
  loading.value = true
  errorMessage.value = ''
  
  try {
    await authStore.login(form.email, form.password)
    
    // Redirect based on user role
    const userRole = authStore.user?.role
    if (userRole === 'admin') {
      router.push('/')
    } else if (userRole === 'mentor') {
      router.push('/')
    } else if (userRole === 'alumni') {
      router.push('/')
    } else {
      router.push('/')
    }
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = error.response?.data?.message || 'Login failed. Please check your credentials.'
  } finally {
    loading.value = false
  }
}
</script>
