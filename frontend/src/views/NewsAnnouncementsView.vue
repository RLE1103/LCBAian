<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">News and Announcements</h1>
        <p class="text-gray-600 mt-1">Official updates and announcements</p>
      </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
      <div v-if="loading" class="text-center py-8 text-gray-500">Loading...</div>
      <div v-else-if="posts.length === 0" class="text-center py-12 text-gray-500">No announcements yet</div>
      <div v-else class="space-y-4">
        <div v-for="post in posts" :key="post.post_id" class="border border-gray-200 rounded-lg p-4">
          <div class="flex items-center justify-between mb-2">
            <div>
              <h3 class="font-semibold text-gray-900">{{ post.title || 'Announcement' }}</h3>
              <p class="text-sm text-gray-600">By {{ post.user?.first_name }} {{ post.user?.last_name }}</p>
            </div>
            <span class="text-xs text-gray-500">{{ formatDate(post.created_at) }}</span>
          </div>
          <p class="text-gray-800">{{ post.content }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../config/api'

const posts = ref([])
const loading = ref(false)

const loadPosts = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/posts')
    if (res?.data?.success && Array.isArray(res.data.data)) {
      posts.value = res.data.data
    } else {
      posts.value = []
    }
  } catch (e) {
    console.error('Error loading posts', e)
    posts.value = []
  } finally {
    loading.value = false
  }
}

const formatDate = (d) => {
  if (!d) return ''
  return new Date(d).toLocaleString()
}

onMounted(() => loadPosts())
</script>


