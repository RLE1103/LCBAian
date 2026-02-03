<template>
  <div class="animate-pulse">
    <!-- Card Skeleton -->
    <div v-if="type === 'card'" class="bg-white rounded-lg shadow-md p-6">
      <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
      <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
      <div class="h-3 bg-gray-200 rounded w-5/6 mb-2"></div>
      <div class="h-3 bg-gray-200 rounded w-4/6"></div>
    </div>

    <!-- List Item Skeleton -->
    <div v-else-if="type === 'list'" class="flex items-center gap-4 p-4 bg-white rounded-lg shadow-md">
      <div class="w-12 h-12 bg-gray-200 rounded-full flex-shrink-0"></div>
      <div class="flex-1">
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
        <div class="h-3 bg-gray-200 rounded w-3/4"></div>
      </div>
    </div>

    <!-- Table Row Skeleton -->
    <div v-else-if="type === 'table-row'" class="border-b border-gray-200">
      <div class="px-6 py-4 flex items-center gap-4">
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
      </div>
    </div>

    <!-- Avatar Skeleton -->
    <div v-else-if="type === 'avatar'" class="w-12 h-12 bg-gray-200 rounded-full"></div>

    <!-- Text Line Skeleton -->
    <div v-else-if="type === 'text'" class="h-4 bg-gray-200 rounded" :class="widthClass"></div>

    <!-- Custom  Skeleton -->
    <div v-else>
      <div class="h-20 bg-gray-200 rounded"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'card',
    validator: (value) => ['card', 'list', 'table-row', 'avatar', 'text', 'custom'].includes(value)
  },
  count: {
    type: Number,
    default: 1
  },
  width: {
    type: String,
    default: 'full',
    validator: (value) => ['full', '3/4', '1/2', '1/4'].includes(value)
  }
})

const widthClass = computed(() => {
  const widths = {
    'full': 'w-full',
    '3/4': 'w-3/4',
    '1/2': 'w-1/2',
    '1/4': 'w-1/4'
  }
  return widths[props.width]
})
</script>

<style scoped>
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
