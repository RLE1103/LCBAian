<template>
  <div :class="[bg, 'rounded-2xl p-6 flex flex-col items-center card-shadow']">
    <span class="text-4xl mb-2">{{ icon }}</span>
    <div class="text-2xl font-bold">{{ label }}</div>
  </div>
</template>

<script setup>
defineProps({
  icon: String,
  label: String,
  bg: {
    type: String,
    default: 'bg-white text-black'
  }
})
</script>
