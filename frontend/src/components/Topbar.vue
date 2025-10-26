<template>
  <header class="flex items-center justify-between bg-blue-800 p-4 rounded-b-2xl shadow-lg mx-4 mt-4 mb-2">
    <div class="flex items-center gap-2">
      <button class="text-white text-2xl mr-4">☰</button>
      <select class="bg-blue-700 text-white rounded px-3 py-2 font-semibold focus:outline-none shadow">
        <option>All Alumni</option>
      </select>
      <input type="text" placeholder="Search..." class="ml-4 px-4 py-2 rounded-lg bg-blue-700 text-white focus:outline-none shadow placeholder:text-blue-200 w-64" />
    </div>
    <div class="flex items-center gap-4">
      <button class="relative"><span class="text-white text-2xl">🔔</span><span class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full px-1">1</span></button>
      <button class="text-yellow-400 text-2xl">🛠️</button>
      <button class="relative"><span class="text-green-400 text-2xl">✉️</span><span class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full px-1">1</span></button>
    </div>
  </header>
</template>

<script setup>
// Topbar logic (if needed)
</script>
