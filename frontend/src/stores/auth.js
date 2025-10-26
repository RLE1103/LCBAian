import { defineStore } from 'pinia'
import axios from '../config/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token'),
    isAuthenticated: false,
    loading: false
  }),

  getters: {
    isAdmin: (state) => state.user?.role === 'admin',
    isMentor: (state) => state.user?.role === 'mentor',
    isAlumni: (state) => state.user?.role === 'alumni',
    userRole: (state) => state.user?.role,
    fullName: (state) => state.user ? `${state.user.first_name} ${state.user.last_name}` : ''
  },

  actions: {
    // Set up axios defaults
    setupAxios() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    },

    // Login user
    async login(email, password) {
      this.loading = true
      try {
        const response = await axios.post('/api/login', {
          email,
          password
        })

        const { token, user } = response.data
        
        // Store token and user data
        this.token = token
        this.user = user
        this.isAuthenticated = true
        
        // Save to localStorage
        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_data', JSON.stringify(user))
        
        // Set up axios headers
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { token, user }
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
    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      
      // Clear localStorage
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      
      // Remove axios headers
      delete axios.defaults.headers.common['Authorization']
    },

    // Check if user is authenticated
    async checkAuth() {
      if (!this.token) {
        this.logout()
        return false
      }

      try {
        // Set up axios headers
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        // Fetch user data
        const response = await axios.get('/api/user')
        this.user = response.data
        this.isAuthenticated = true
        
        return true
      } catch (error) {
        console.error('Auth check failed:', error)
        this.logout()
        return false
      }
    },

    // Initialize auth from localStorage
    async initAuth() {
      const token = localStorage.getItem('auth_token')
      const userData = localStorage.getItem('user_data')
      
      if (token && userData) {
        this.token = token
        this.user = JSON.parse(userData)
        this.isAuthenticated = true
        this.setupAxios()
        
        // Verify token is still valid
        return await this.checkAuth()
      }
      
      return false
    },

    // Update user profile
    async updateProfile(userData) {
      try {
        const response = await axios.put('/api/user/profile', userData)
        this.user = { ...this.user, ...response.data }
        
        // Update localStorage
        localStorage.setItem('user_data', JSON.stringify(this.user))
        
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
    }
  }
})
