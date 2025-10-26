<template>
  <div :class="[bg, 'rounded-xl p-3 mb-3 flex justify-between items-center card-shadow']">
    <span class="font-semibold">{{ name }}</span>
    <slot />
  </div>
</template>

<script setup>
defineProps({
  name: String,
  bg: {
    type: String,
    default: 'bg-blue-700 text-white'
  }
})
</script>
