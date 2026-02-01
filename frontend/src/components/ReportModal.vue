<template>
  <!-- Modal Overlay -->
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
      <!-- Header -->
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-900">
          <span class="mr-2">🚩</span>Report {{ entityTypeLabel }}
        </h2>
        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitReport">
        <div class="space-y-4">
          <!-- Reason Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Report <span class="text-red-500">*</span></label>
            <select v-model="form.reason" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
              <option value="">Select a reason</option>
              <option value="spam">Spam or Advertising</option>
              <option value="inappropriate_content">Inappropriate Content</option>
              <option value="scam_fraud">Scam or Fraud</option>
              <option value="harassment">Harassment or Bullying</option>
              <option value="false_information">False or Misleading Information</option>
              <option value="other">Other</option>
            </select>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Details</label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
              placeholder="Please provide more information about why you're reporting this..."
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">Optional, but helps us review faster</p>
          </div>

          <!-- Warning -->
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3">
            <p class="text-sm text-gray-700">
              <strong>Important:</strong> False or malicious reports may result in action against your account.
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="submitting || !form.reason"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Submitting...' : 'Submit Report' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from '../config/api'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  entityType: {
    type: String,
    required: true,
    validator: (value) => ['job_post', 'user', 'post', 'comment'].includes(value)
  },
  entityId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['close', 'submitted'])

const form = ref({
  reason: '',
  description: ''
})

const submitting = ref(false)

const entityTypeLabel = computed(() => {
  const labels = {
    job_post: 'Job Post',
    user: 'User',
    post: 'Post',
    comment: 'Comment'
  }
  return labels[props.entityType] || 'Content'
})

// Reset form when modal opens
watch(() => props.isOpen, (newValue) => {
  if (newValue) {
    form.value = {
      reason: '',
      description: ''
    }
  }
})

const closeModal = () => {
  emit('close')
}

const submitReport = async () => {
  if (!form.value.reason) return

  submitting.value = true
  
  try {
    const payload = {
      reported_entity_type: props.entityType,
      reported_entity_id: props.entityId,
      reason: form.value.reason,
      description: form.value.description || null
    }

    const response = await axios.post('/api/reports', payload)
    
    if (response?.data?.success) {
      emit('submitted')
      closeModal()
      alert('Report submitted successfully. Our team will review it shortly.')
    }
  } catch (error) {
    console.error('Error submitting report:', error)
    alert(error.response?.data?.message || 'Failed to submit report. Please try again.')
  } finally {
    submitting.value = false
  }
}
</script>
