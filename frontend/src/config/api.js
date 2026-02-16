// API Configuration
const SPA_ORIGIN = `${window.location.protocol}//${window.location.hostname}`
const ENV_API_URL = import.meta.env.VITE_API_URL
const API_BASE_URL = ENV_API_URL || `${SPA_ORIGIN}:8000`

// Configure axios defaults
import axios from 'axios'

axios.defaults.baseURL = API_BASE_URL
axios.defaults.headers.common['Content-Type'] = 'application/json'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'
axios.defaults.withXSRFToken = true

// Enable credentials (cookies) for CSRF protection
axios.defaults.withCredentials = true

// Helper function to get CSRF cookie
export const getCsrfCookie = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie')
    const token = document.cookie
      .split('; ')
      .find((row) => row.startsWith('XSRF-TOKEN='))
      ?.split('=')[1]
    if (token) {
      axios.defaults.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(token)
    }
  } catch (error) {
    console.error('Failed to get CSRF cookie:', error)
  }
}

// Request interceptor
axios.interceptors.request.use(
  (config) => {
    const token = document.cookie
      .split('; ')
      .find((row) => row.startsWith('XSRF-TOKEN='))
      ?.split('=')[1]
    if (token) {
      config.headers = config.headers || {}
      config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
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
      sessionStorage.removeItem('auth_token')

      // Only redirect if not already on login page
      if (!window.location.pathname.includes('/login')) {
        window.location.href = '/login'
      }
    }

    // Handle 419 errors (CSRF token mismatch)
    if (error.response?.status === 419 && error.config && !error.config._retry) {
      error.config._retry = true
      await getCsrfCookie()
      return axios(error.config)
    }

    return Promise.reject(error)
  }
)

export default axios
