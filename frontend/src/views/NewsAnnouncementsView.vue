<template>
  <div class="p-4 md:p-6">
    <div class="max-w-3xl mx-auto">
      <!-- Page Header -->
      <div class="mb-4 md:mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">News and Announcements</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Share updates with the LCBA community</p>
      </div>

      <!-- Search Bar -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-4">
        <div class="relative">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search announcements by content or author..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Post Composer (Facebook-style) - Admin Only -->
      <div v-if="isAdmin" class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex items-start gap-3 mb-4">
          <!-- User Avatar -->
          <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0 text-sm md:text-base overflow-hidden">
            <img
              v-if="authStore.user?.profile_picture"
              :src="getProfilePictureUrl(authStore.user.profile_picture)"
              class="w-full h-full object-cover"
            />
            <span v-else>{{ getUserInitials() }}</span>
          </div>
          
          <!-- Post Input -->
          <div class="flex-1">
            <textarea
              v-model="newPost.content"
              @focus="isComposerExpanded = true"
              :rows="isComposerExpanded ? 4 : 1"
              placeholder="What's New?"
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
        <div
          v-else
          v-for="post in filteredPosts"
          :key="post.post_id"
          class="bg-white rounded-lg shadow-md overflow-hidden cursor-pointer hover:shadow-lg transition-shadow"
          @click="openPost(post)"
        >
          <!-- Post Header -->
          <div class="p-4 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0 overflow-hidden">
              <img
                v-if="post.user?.profile_picture"
                :src="getProfilePictureUrl(post.user.profile_picture)"
                class="w-full h-full object-cover"
              />
              <span v-else>{{ getPostUserInitials(post.user) }}</span>
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

        </div>
      </div>
    </div>
  </div>

  <div v-if="showPostModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showPostModal = false">
    <div class="bg-white rounded-lg w-full max-w-3xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto border-2 border-black shadow-2xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Announcement</h2>
          </div>
          <button @click="showPostModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0 overflow-hidden">
            <img
              v-if="selectedPost?.user?.profile_picture"
              :src="getProfilePictureUrl(selectedPost.user.profile_picture)"
              class="w-full h-full object-cover"
            />
            <span v-else>{{ getPostUserInitials(selectedPost?.user) }}</span>
          </div>
          <div>
            <p class="font-semibold text-gray-900">{{ selectedPost?.user?.first_name }} {{ selectedPost?.user?.last_name }}</p>
            <p class="text-sm text-gray-600">Posted {{ formatDate(selectedPost?.created_at) }}</p>
          </div>
        </div>

        <div class="space-y-4">
          <p class="text-gray-800 whitespace-pre-wrap">{{ selectedPost?.content }}</p>
          <div v-if="selectedPost?.image_path" class="w-full">
            <img :src="getImageUrl(selectedPost.image_path)" class="w-full h-auto object-contain max-h-[80vh] rounded-lg border border-gray-200 bg-gray-50" />
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
import { useToast } from '../composables/useToast'

const toast = useToast()
const authStore = useAuthStore()
const posts = ref([])
const loading = ref(false)
const posting = ref(false)
const isComposerExpanded = ref(false)
const imageInput = ref(null)
const imagePreview = ref(null)
const selectedImage = ref(null)
const searchQuery = ref('')
const selectedPost = ref(null)
const showPostModal = ref(false)

// Check if user is admin
const isAdmin = computed(() => authStore.user?.role === 'admin')

// Filter posts based on search query
const filteredPosts = computed(() => {
  if (!searchQuery.value.trim()) {
    return posts.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return posts.value.filter(post => {
    const content = post.content?.toLowerCase() || ''
    const authorName = `${post.user?.first_name || ''} ${post.user?.last_name || ''}`.toLowerCase()
    return content.includes(query) || authorName.includes(query)
  })
})

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

const openPost = (post) => {
  selectedPost.value = post
  showPostModal.value = true
}

const submitPost = async () => {
  if (!newPost.value.content.trim()) {
    toast.warning('Please enter some content for your post', 'Validation Error')
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
      toast.success('Post created successfully!', 'Success')
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
    toast.error('Failed to create post: ' + (e.response?.data?.message || e.message), 'Error')
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

const getProfilePictureUrl = (profilePicture) => {
  if (!profilePicture) return ''
  const baseUrl = axios.defaults.baseURL || 'http://localhost:8000'
  return `${baseUrl}/uploads/profile_pictures/${profilePicture}`
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

onMounted(async () => {
  const isAuthenticated = await authStore.checkAuth()
  if (!isAuthenticated) {
    window.location.href = '/login'
    return
  }
  await loadPosts()
})
</script>
