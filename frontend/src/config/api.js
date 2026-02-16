// API Configuration
const SPA_ORIGIN = `${window.location.protocol}//${window.location.hostname}`
const ENV_API_URL = import.meta.env.VITE_API_URL
const API_BASE_URL = ENV_API_URL || `${SPA_ORIGIN}:8000`

// Configure axios defaults
import axios from 'axios'

axios.defaults.baseURL = API_BASE_URL
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept'] = 'application/json'
const storedToken = sessionStorage.getItem('auth_token')
if (storedToken) {
  axios.defaults.headers.common.Authorization = `Bearer ${storedToken}`
}

// Request interceptor
axios.interceptors.request.use(
  (config) => {
    const token = sessionStorage.getItem('auth_token')
    if (token) {
      config.headers = config.headers || {}
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  async (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
axios.interceptors.response.use(
  (response) => {
    return response
  },
  async (error) => {
    // Handle 401 errors (unauthorized)
    if (error.response?.status === 401) {
      // Clear auth data
      sessionStorage.removeItem('user_data')

      // Only redirect if not already on login page
      if (!window.location.pathname.includes('/login')) {
        window.location.href = '/login'
      }
    }

    return Promise.reject(error)
  }
)

export default axios
