<template>
  <div class="inline-block relative">
    <button
      @click="toggle"
      @mouseenter="show"
      @mouseleave="hide"
      type="button"
      class="inline-flex items-center justify-center w-5 h-5 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full"
      :aria-label="'Help: ' + title"
      aria-haspopup="true"
      :aria-expanded="visible"
    >
      <svg class="w-full h-full" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
      </svg>
    </button>

    <!-- Tooltip -->
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 translate-y-1"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 translate-y-1"
    >
      <div
        v-if="visible"
        :class="[
          'absolute z-50 w-64 px-4 py-3 text-sm bg-gray-900 text-white rounded-lg shadow-xl',
          positionClass
        ]"
        role="tooltip"
      >
        <!-- Arrow -->
        <div 
          class="absolute w-2 h-2 bg-gray-900 transform rotate-45"
          :class="arrowClass"
        ></div>

        <!-- Content -->
        <div class="relative">
          <p v-if="title" class="font-semibold mb-1">{{ title }}</p>
          <p class="text-gray-200">{{ content }}</p>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  content: {
    type: String,
    required: true
  },
  position: {
    type: String,
    default: 'top',
    validator: (value) => ['top', 'bottom', 'left', 'right'].includes(value)
  },
  trigger: {
    type: String,
    default: 'hover',
    validator: (value) => ['hover', 'click'].includes(value)
  }
})

const visible = ref(false)
let hideTimeout = null

const toggle = () => {
  if (props.trigger === 'click') {
    visible.value = !visible.value
  }
}

const show = () => {
  if (props.trigger === 'hover') {
    if (hideTimeout) clearTimeout(hideTimeout)
    visible.value = true
  }
}

const hide = () => {
  if (props.trigger === 'hover') {
    hideTimeout = setTimeout(() => {
      visible.value = false
    }, 200)
  }
}

const positionClass = computed(() => {
  const positions = {
    top: 'bottom-full mb-2 left-1/2 transform -translate-x-1/2',
    bottom: 'top-full mt-2 left-1/2 transform -translate-x-1/2',
    left: 'right-full mr-2 top-1/2 transform -translate-y-1/2',
    right: 'left-full ml-2 top-1/2 transform -translate-y-1/2'
  }
  return positions[props.position]
})

const arrowClass = computed(() => {
  const arrows = {
    top: 'top-full left-1/2 transform -translate-x-1/2 -mt-1',
    bottom: 'bottom-full left-1/2 transform -translate-x-1/2 -mb-1',
    left: 'left-full top-1/2 transform -translate-y-1/2 -ml-1',
    right: 'right-full top-1/2 transform -translate-y-1/2 -mr-1'
  }
  return arrows[props.position]
})
</script>
