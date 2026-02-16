import { defineStore } from 'pinia'
import axios from '../config/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(sessionStorage.getItem('user_data')) || null,
    isAuthenticated: !!sessionStorage.getItem('user_data'),
    loading: false,
    pendingWarning: null,
    skipAuthCheckUntil: 0
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isAlumni: (state) => state.user?.role === 'alumni',
    userRole: (state) => state.user?.role,
    fullName: (state) => state.user ? `${state.user.first_name} ${state.user.last_name}` : ''
  },

  actions: {
    // Login user
    async login(email, password) {
      this.loading = true
      try {
        const response = await axios.post('/api/login', {
          email,
          password,
          device_name: 'web'
        })

        const { user, warning, token } = response.data

        // Store user data
        this.user = user
        this.isAuthenticated = true
        this.pendingWarning = warning || null

        // Save user data to localStorage for persistence
        sessionStorage.setItem('user_data', JSON.stringify(user))
        if (token) {
          sessionStorage.setItem('auth_token', token)
          axios.defaults.headers.common.Authorization = `Bearer ${token}`
        }

        return { user, warning }
      } catch (error) {
        this.logout()
        throw error
      } finally {
        this.loading = false
      }
    },

    // Register user
    async register(userData) {
      this.loading = true
      try {
        const response = await axios.post('/api/register', userData)
        return response.data
      } catch (error) {
        throw error
      } finally {
        this.loading = false
      }
    },

    // Logout user
    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (error) {
        if (![401, 419].includes(error.response?.status)) {
          console.error('Logout API call failed:', error)
        }
      }

      this.user = null
      this.isAuthenticated = false
      this.pendingWarning = null
      this.skipAuthCheckUntil = Date.now() + 5000

      // Clear localStorage
      sessionStorage.removeItem('user_data')
      sessionStorage.removeItem('auth_token')
      delete axios.defaults.headers.common.Authorization
    },

    // Check if user is authenticated via session
    async checkAuth() {
      if (Date.now() < this.skipAuthCheckUntil) {
        return false
      }
      const token = sessionStorage.getItem('auth_token')
      if (token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
      } else {
        return false
      }
      try {
        // Fetch user data from backend to verify session
        const response = await axios.get('/api/user')
        this.user = response.data
        this.isAuthenticated = true
        sessionStorage.setItem('user_data', JSON.stringify(this.user))
        return true
      } catch (error) {
        const status = error.response?.status
        // If 401 Unauthorized or 419 CSRF mismatch, we are strictly not logged in
        if (status === 401 || status === 419) {
          this.user = null
          this.isAuthenticated = false
          sessionStorage.removeItem('user_data')
        }
        return false
      }
    },

    // Initialize auth
    async initAuth() {
      if (!this.user) {
        this.isAuthenticated = false
        return false
      }

      return await this.checkAuth()
    },

    // Update user profile
    async updateProfile(userData) {
      try {
        const response = await axios.put('/api/user/profile', userData)
        this.user = { ...this.user, ...response.data }

        // Update localStorage
        sessionStorage.setItem('user_data', JSON.stringify(this.user))

        return response.data
      } catch (error) {
        throw error
      }
    },

    // Change password
    async changePassword(currentPassword, newPassword) {
      try {
        const response = await axios.put('/api/user/password', {
          current_password: currentPassword,
          password: newPassword,
          password_confirmation: newPassword
        })
        return response.data
      } catch (error) {
        throw error
      }
    },

    async fetchPendingWarning() {
      try {
        const response = await axios.get('/api/user/warnings/pending')
        this.pendingWarning = response.data?.warning || null
        return this.pendingWarning
      } catch (error) {
        this.pendingWarning = null
        return null
      }
    },

    async acknowledgeWarning(warningId) {
      const response = await axios.post(`/api/user/warnings/${warningId}/acknowledge`)
      this.pendingWarning = null
      return response.data
    }
  }
})
