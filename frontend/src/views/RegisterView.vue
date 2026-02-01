<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 border-4 border-gray-300">
      <!-- Logo and Header -->
      <div class="text-center mb-8">
        <div class="flex items-center justify-center mb-4">
          <img src="/src/assets/images/LCBAlogo.png" alt="LCBA Logo" class="w-30 h-30 rounded object-cover" />
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Join LCBAConnect+</h1>
        <p class="text-gray-600 mt-2">Alumni Sign-Up</p>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="handleRegister" class="space-y-6">
        <div>
          <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
          <input
            id="firstName"
            v-model="form.first_name"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="First name"
            :class="{ 'border-red-500': errors.first_name }"
          />
          <p v-if="errors.first_name" class="text-red-500 text-sm mt-1">{{ errors.first_name }}</p>
        </div>

        <div>
          <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
          <input
            id="lastName"
            v-model="form.last_name"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="Last name"
            :class="{ 'border-red-500': errors.last_name }"
          />
          <p v-if="errors.last_name" class="text-red-500 text-sm mt-1">{{ errors.last_name }}</p>
        </div>

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
              placeholder="Create a password"
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

        <div>
          <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
          <input
            id="confirmPassword"
            v-model="form.password_confirmation"
            type="password"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="Confirm your password"
            :class="{ 'border-red-500': errors.password_confirmation }"
          />
          <p v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ errors.password_confirmation }}</p>
        </div>

        <div>
          <label for="program" class="block text-sm font-medium text-gray-700 mb-2">Program</label>
          <select
            id="program"
            v-model="form.program"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            :class="{ 'border-red-500': errors.program }"
          >
            <option value="">Select your program</option>
            <optgroup label="Undergraduate Programs">
              <option value="AB Political Science">AB Political Science</option>
              <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
              <option value="Bachelor of Secondary Education (Majors in English, Mathematics, Social Studies, Science, Filipino)">Bachelor of Secondary Education (Majors in English, Mathematics, Social Studies, Science, Filipino)</option>
              <option value="BS Computer Science">BS Computer Science</option>
              <option value="BS Psychology">BS Psychology</option>
              <option value="BS Computer Engineering">BS Computer Engineering</option>
              <option value="BS Accountancy">BS Accountancy</option>
              <option value="BS Business Administration (Majors in Human Resources Management, Marketing Management)">BS Business Administration (Majors in Human Resources Management, Marketing Management)</option>
              <option value="BS Entrepreneurship in Culinary Arts">BS Entrepreneurship in Culinary Arts</option>
            </optgroup>
            <optgroup label="Master's Programs">
              <option value="Master of Arts in Education Major in Guidance and Counseling">Master of Arts in Education Major in Guidance and Counseling</option>
              <option value="Master in Business Administration">Master in Business Administration</option>
              <option value="Master of Science in Psychology">Master of Science in Psychology</option>
              <option value="Master of Arts in Education Major in Administration and Supervision">Master of Arts in Education Major in Administration and Supervision</option>
              <option value="Master of Arts in Education Major in English">Master of Arts in Education Major in English</option>
              <option value="Master of Arts in Education Major in Filipino">Master of Arts in Education Major in Filipino</option>
              <option value="Master of Arts in Education Major in Social Studies">Master of Arts in Education Major in Social Studies</option>
              <option value="Master in Management major in Public Administration">Master in Management major in Public Administration</option>
            </optgroup>
            <optgroup label="Other">
              <option value="Senior High School Graduate">Senior High School Graduate</option>
              <option value="High School Graduate">High School Graduate</option>
              <option value="Elementary Graduate">Elementary Graduate</option>
            </optgroup>
          </select>
          <p v-if="errors.program" class="text-red-500 text-sm mt-1">{{ errors.program }}</p>
        </div>

        <div>
          <label for="batch" class="block text-sm font-medium text-gray-700 mb-2">Graduation Year</label>
          <input
            id="batch"
            v-model="form.batch"
            type="number"
            required
            min="1990"
            :max="new Date().getFullYear()"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            placeholder="e.g., 2020"
            :class="{ 'border-red-500': errors.batch }"
          />
          <p v-if="errors.batch" class="text-red-500 text-sm mt-1">{{ errors.batch }}</p>
        </div>

        <div>
          <label for="highestAttainment" class="block text-sm font-medium text-gray-700 mb-2">Highest Educational Attainment</label>
          <select
            id="highestAttainment"
            v-model="form.highest_educational_attainment"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
            :class="{ 'border-red-500': errors.highest_educational_attainment }"
          >
            <option value="">Select level</option>
            <option value="high_school">High School</option>
            <option value="senior_high">Senior High School</option>
            <option value="bachelors">Bachelor's Degree</option>
            <option value="masters">Master's Degree</option>
            <option value="doctorate">Doctorate</option>
          </select>
          <p v-if="errors.highest_educational_attainment" class="text-red-500 text-sm mt-1">{{ errors.highest_educational_attainment }}</p>
        </div>

        <div class="flex items-center">
          <input
            id="terms"
            v-model="form.terms"
            type="checkbox"
            required
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          />
          <label for="terms" class="ml-2 text-sm text-gray-600">
            I agree to the 
            <router-link to="/terms" target="_blank" class="text-blue-600 hover:text-blue-800 underline">Community Guidelines & Terms of Use</router-link>
          </label>
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
            Creating account...
          </span>
          <span v-else>Create Account</span>
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

      <!-- Success Message -->
      <div v-if="successMessage" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex">
          <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <div class="ml-3">
            <p class="text-sm text-green-800">{{ successMessage }}</p>
          </div>
        </div>
      </div>

      <!-- Login Link -->
      <div class="mt-6 text-center">
        <p class="text-gray-600">
          Already have an account?
          <router-link to="/login" class="text-blue-600 hover:text-blue-800 font-medium">
            Sign in here
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
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'alumni', // Registration is only for alumni
  program: '',
  batch: '',
  highest_educational_attainment: '',
  terms: false
})

// UI state
const loading = ref(false)
const showPassword = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const errors = reactive({})

// Validation
const validateForm = () => {
  // Clear previous errors
  Object.keys(errors).forEach(key => errors[key] = '')
  
  let isValid = true
  
  if (!form.first_name.trim()) {
    errors.first_name = 'First name is required'
    isValid = false
  }
  
  if (!form.last_name.trim()) {
    errors.last_name = 'Last name is required'
    isValid = false
  }
  
  if (!form.email.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!form.email.includes('@')) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }
  
  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
    isValid = false
  }
  
  if (!form.password_confirmation) {
    errors.password_confirmation = 'Please confirm your password'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
    isValid = false
  }
  
  if (!form.program) {
    errors.program = 'Program is required'
    isValid = false
  }
  
  if (!form.batch) {
    errors.batch = 'Graduation year is required'
    isValid = false
  }
  
  if (!form.highest_educational_attainment) {
    errors.highest_educational_attainment = 'Highest educational attainment is required'
    isValid = false
  }
  
  if (!form.terms) {
    errorMessage.value = 'You must agree to the terms and conditions'
    isValid = false
  }
  
  return isValid
}

// Registration handler
const handleRegister = async () => {
  if (!validateForm()) return
  
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''
  
  try {
    await authStore.register(form)
    successMessage.value = 'Account created successfully! Redirecting to login...'
    
    // Redirect to login after 2 seconds
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  } catch (error) {
    console.error('Registration error:', error)
    if (error.response?.data?.errors) {
      // Handle validation errors from backend
      Object.keys(error.response.data.errors).forEach(key => {
        errors[key] = error.response.data.errors[key][0]
      })
    } else {
      errorMessage.value = error.response?.data?.message || 'Registration failed. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>
