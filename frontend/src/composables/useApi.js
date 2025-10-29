import { ref } from 'vue'
import axios from '../config/api'

export function useApi(endpoint) {
  const data = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchData = async (params = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.get(`/api/${endpoint}`, { params })
      if (response.data.success) {
        data.value = response.data.data
      } else {
        error.value = response.data.message || 'Failed to fetch data'
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      console.error(`Error fetching ${endpoint}:`, err)
    } finally {
      loading.value = false
    }
    
    return data.value
  }

  const createData = async (payload) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.post(`/api/${endpoint}`, payload)
      if (response.data.success) {
        return response.data.data
      } else {
        error.value = response.data.message || 'Failed to create data'
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      console.error(`Error creating ${endpoint}:`, err)
    } finally {
      loading.value = false
    }
    
    return null
  }

  const updateData = async (id, payload) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await axios.put(`/api/${endpoint}/${id}`, payload)
      if (response.data.success) {
        return response.data.data
      } else {
        error.value = response.data.message || 'Failed to update data'
      }
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      console.error(`Error updating ${endpoint}:`, err)
    } finally {
      loading.value = false
    }
    
    return null
  }

  return {
    data,
    loading,
    error,
    fetchData,
    createData,
    updateData
  }
}

