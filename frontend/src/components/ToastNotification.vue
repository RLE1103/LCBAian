<template>
  <Teleport to="body">
    <Transition name="toast-fade">
      <div 
        v-if="visible"
        :class="[
          'fixed z-[9999] max-w-md w-full pointer-events-auto',
          positionClass
        ]"
      >
        <div 
          :class="[
            'rounded-lg shadow-2xl p-4 flex items-start gap-3',
            typeClass
          ]"
          role="alert"
          :aria-live="type === 'error' ? 'assertive' : 'polite'"
        >
          <!-- Icon -->
          <div class="flex-shrink-0">
            <svg v-if="type === 'success'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <svg v-else-if="type === 'error'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <svg v-else-if="type === 'warning'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
          </div>

          <!-- Content -->
          <div class="flex-1 min-w-0">
            <p v-if="title" class="font-semibold text-sm">{{ title }}</p>
            <p class="text-sm" :class="{ 'mt-1': title }">{{ message }}</p>
          </div>

          <!-- Close Button -->
          <button
            @click="close"
            class="flex-shrink-0 ml-2 inline-flex text-current opacity-60 hover:opacity-100 transition-opacity focus:outline-none focus:ring-2 focus:ring-offset-2 rounded"
            :class="focusRingClass"
            aria-label="Close"
          >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  id: {
    type: [String, Number],
    required: true
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  duration: {
    type: Number,
    default: 5000
  },
  position: {
    type: String,
    default: 'top-right',
    validator: (value) => ['top-left', 'top-right', 'top-center', 'bottom-left', 'bottom-right', 'bottom-center'].includes(value)
  }
})

const emit = defineEmits(['close'])

const visible = ref(false)

const typeClass = computed(() => {
  const classes = {
    success: 'bg-green-50 text-green-800 border border-green-200',
    error: 'bg-red-50 text-red-800 border border-red-200',
    warning: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
    info: 'bg-blue-50 text-blue-800 border border-blue-200'
  }
  return classes[props.type]
})

const focusRingClass = computed(() => {
  const classes = {
    success: 'focus:ring-green-500',
    error: 'focus:ring-red-500',
    warning: 'focus:ring-yellow-500',
    info: 'focus:ring-blue-500'
  }
  return classes[props.type]
})

const positionClass = computed(() => {
  const positions = {
    'top-left': 'top-4 left-4',
    'top-right': 'top-4 right-4',
    'top-center': 'top-4 left-1/2 transform -translate-x-1/2',
    'bottom-left': 'bottom-4 left-4',
    'bottom-right': 'bottom-4 right-4',
    'bottom-center': 'bottom-4 left-1/2 transform -translate-x-1/2'
  }
  return positions[props.position]
})

let timeoutId = null

const close = () => {
  visible.value = false
  setTimeout(() => {
    emit('close', props.id)
  }, 300) // Match transition duration
}

onMounted(() => {
  visible.value = true
  
  if (props.duration > 0) {
    timeoutId = setTimeout(() => {
      close()
    }, props.duration)
  }
})

// Cleanup timeout on unmount
const clearAutoClose = () => {
  if (timeoutId) {
    clearTimeout(timeoutId)
    timeoutId = null
  }
}

// Pause auto-close on hover
const pauseAutoClose = () => {
  clearAutoClose()
}

// Resume auto-close on mouse leave
const resumeAutoClose = () => {
  if (props.duration > 0) {
    timeoutId = setTimeout(() => {
      close()
    }, 2000) // Give 2 more seconds
  }
}
</script>

<style scoped>
.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.3s ease;
}

.toast-fade-enter-from {
  opacity: 0;
  transform: translateY(-1rem);
}

.toast-fade-leave-to {
  opacity: 0;
  transform: translateY(-0.5rem);
}
</style>
