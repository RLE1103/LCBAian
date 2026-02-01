<template>
  <div class="p-4 md:p-6">
    <div class="max-w-3xl mx-auto">
      <!-- Page Header -->
      <div class="mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">News and Announcements</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Share updates with the LCBA community</p>
      </div>

      <!-- Post Composer (Facebook-style) - Admin Only -->
      <div v-if="isAdmin" class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-start gap-3 mb-4">
          <!-- User Avatar -->
          <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0 text-sm md:text-base">
            {{ getUserInitials() }}
          </div>
          
          <!-- Post Input -->
          <div class="flex-1">
            <textarea
              v-model="newPost.content"
              @focus="isComposerExpanded = true"
              :rows="isComposerExpanded ? 4 : 1"
              placeholder="What's on your mind?"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none transition-all"
            ></textarea>
            
            <!-- Image Preview -->
            <div v-if="imagePreview" class="mt-3 relative">
              <img :src="imagePreview" class="w-full rounded-lg max-h-96 object-cover border border-gray-200" />
              <button 
                @click="removeImage"
                class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 shadow-lg"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Composer Actions -->
        <div v-if="isComposerExpanded" class="flex items-center justify-between pt-3 border-t border-gray-200">
          <div class="flex items-center gap-2">
            <!-- Image Upload Button -->
            <label class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition-colors">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <span class="text-sm font-medium text-gray-700">Photo</span>
              <input 
                type="file" 
                ref="imageInput"
                @change="handleImageSelect"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                class="hidden"
              />
            </label>
          </div>

          <!-- Post Button -->
          <button 
            @click="submitPost"
            :disabled="!newPost.content.trim() || posting"
            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
          >
            {{ posting ? 'Posting...' : 'Post' }}
          </button>
        </div>
      </div>

      <!-- Posts Feed -->
      <div class="space-y-4">
        <div v-if="loading" class="text-center py-8 text-gray-500">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
          <p>Loading posts...</p>
        </div>
        
        <div v-else-if="posts.length === 0" class="text-center py-12 text-gray-500 bg-white rounded-lg shadow-md">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
          </svg>
          <p class="font-medium">No announcements yet</p>
          <p class="text-sm mt-1">Be the first to share something with the community!</p>
        </div>
        
        <!-- Post Cards -->
        <div v-else v-for="post in posts" :key="post.post_id" class="bg-white rounded-lg shadow-md overflow-hidden">
          <!-- Post Header -->
          <div class="p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0">
              {{ getPostUserInitials(post.user) }}
            </div>
            <div class="flex-1">
              <p class="font-semibold text-gray-900">{{ post.user?.first_name }} {{ post.user?.last_name }}</p>
              <p class="text-sm text-gray-600">{{ formatDate(post.created_at) }}</p>
            </div>
          </div>

          <!-- Post Content -->
          <div class="px-4 pb-4">
            <p class="text-gray-800 whitespace-pre-wrap">{{ post.content }}</p>
          </div>

          <!-- Post Image -->
          <div v-if="post.image_path" class="w-full">
            <img :src="getImageUrl(post.image_path)" class="w-full object-cover max-h-96" />
          </div>

          <!-- Post Actions (Optional for future) -->
          <div class="px-4 py-3 border-t border-gray-100 flex items-center gap-4 text-sm text-gray-600">
            <button class="flex items-center gap-2 hover:text-blue-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
              </svg>
              <span>Like</span>
            </button>
            <button class="flex items-center gap-2 hover:text-blue-600 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              <span>Comment</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from '../config/api'

const authStore = useAuthStore()
const posts = ref([])
const loading = ref(false)
const posting = ref(false)
const isComposerExpanded = ref(false)
const imageInput = ref(null)
const imagePreview = ref(null)
const selectedImage = ref(null)

// Check if user is admin
const isAdmin = computed(() => authStore.user?.role === 'admin')

const newPost = ref({
  content: '',
  visibility: 'public'  // Changed to public so all users can see
})

const loadPosts = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/posts')
    if (res?.data?.success) {
      posts.value = res.data.data.data || res.data.data || []
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

const handleImageSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedImage.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  selectedImage.value = null
  imagePreview.value = null
  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

const submitPost = async () => {
  if (!newPost.value.content.trim()) {
    alert('Please enter some content for your post')
    return
  }
  
  posting.value = true
  try {
    console.log('Submitting post...', {
      content: newPost.value.content,
      visibility: newPost.value.visibility,
      hasImage: !!selectedImage.value
    })
    
    const formData = new FormData()
    formData.append('content', newPost.value.content)
    formData.append('visibility', newPost.value.visibility)
    
    if (selectedImage.value) {
      formData.append('image', selectedImage.value)
    }

    console.log('Sending request to /api/posts...')
    const res = await axios.post('/api/posts', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    console.log('Response received:', res.data)
    
    if (res?.data?.success) {
      alert('Post created successfully!')
      // Reset form
      newPost.value.content = ''
      newPost.value.visibility = 'public'  // Reset to public
      removeImage()
      isComposerExpanded.value = false
      
      // Reload posts
      console.log('Reloading posts...')
      await loadPosts()
      console.log('Posts reloaded, total:', posts.value.length)
    } else {
      console.error('Post creation failed:', res?.data)
      throw new Error(res?.data?.message || 'Failed to create post')
    }
  } catch (e) {
    console.error('Error creating post:', e)
    console.error('Error response:', e.response?.data)
    alert('Failed to create post: ' + (e.response?.data?.message || e.message))
  } finally {
    posting.value = false
  }
}

const getUserInitials = () => {
  const firstName = authStore.user?.first_name || ''
  const lastName = authStore.user?.last_name || ''
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'U'
}

const getPostUserInitials = (user) => {
  if (!user) return 'U'
  const firstName = user.first_name || ''
  const lastName = user.last_name || ''
  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || 'U'
}

const getImageUrl = (path) => {
  if (!path) return ''
  // Path is stored as 'posts/filename.jpg', serve via storage link
  return `http://localhost:8000/storage/${path}`
}

const formatDate = (d) => {
  if (!d) return ''
  const date = new Date(d)
  const now = new Date()
  const diff = now - date
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)
  
  if (seconds < 60) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  
  return date.toLocaleDateString()
}

onMounted(() => loadPosts())
</script>
