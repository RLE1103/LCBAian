<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'
import Sidebar from './components/Sidebar.vue'

const router = useRouter()
const authStore = useAuthStore()
const isInitialized = ref(false)

onMounted(async () => {
  // Initialize authentication
  await authStore.initAuth()
  isInitialized.value = true
  
  // Redirect to login if not authenticated
  if (!authStore.isAuthenticated) {
    router.push('/login')
  }
})
</script>

<template>
  <div v-if="!isInitialized" class="flex h-screen bg-blue-700 items-center justify-center">
    <div class="text-center text-white">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
      <p>Loading...</p>
    </div>
  </div>
  
  <div v-else-if="authStore.isAuthenticated" class="flex h-screen bg-blue-700">
    <Sidebar />
    <main class="flex-1 flex flex-col bg-blue-100 overflow-y-auto">
      <router-view />
    </main>
  </div>
  
  <div v-else class="h-screen bg-blue-700">
    <router-view />
  </div>
</template>

<style>
body {
  margin: 0;
}
</style>
