import { reactive } from 'vue'

const state = reactive({
  toasts: []
})

let toastIdCounter = 0

export function useToast() {
  const show = (options) => {
    const id = ++toastIdCounter
    const toast = {
      id,
      type: options.type || 'info',
      title: options.title || '',
      message: options.message || '',
      duration: options.duration !== undefined ? options.duration : 5000,
      position: options.position || 'top-right'
    }
    
    state.toasts.push(toast)
    return id
  }

  const success = (message, title = '', options = {}) => {
    return show({
      type: 'success',
      message,
      title,
      ...options
    })
  }

  const error = (message, title = '', options = {}) => {
    return show({
      type: 'error',
      message,
      title,
      ...options
    })
  }

  const warning = (message, title = '', options = {}) => {
    return show({
      type: 'warning',
      message,
      title,
      ...options
    })
  }

  const info = (message, title = '', options = {}) => {
    return show({
      type: 'info',
      message,
      title,
      ...options
    })
  }

  const remove = (id) => {
    const index = state.toasts.findIndex(t => t.id === id)
    if (index !== -1) {
      state.toasts.splice(index, 1)
    }
  }

  const clear = () => {
    state.toasts = []
  }

  return {
    toasts: state.toasts,
    show,
    success,
    error,
    warning,
    info,
    remove,
    clear
  }
}
