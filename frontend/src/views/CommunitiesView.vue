<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-8 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white mb-2">Communities</h1>
          <p class="text-blue-100">Connect with alumni through shared interests and passions</p>
        </div>
        <button 
          @click="showCreateModal = true"
          class="bg-white text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 font-semibold shadow-lg transition-all transform hover:scale-105"
        >
          + Create Community
            </button>
          </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
      <div class="flex flex-col lg:flex-row gap-4">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
            <input 
            v-model="searchQuery"
              type="text" 
            placeholder="Search communities by name, description, or tags..."
            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <select v-model="selectedCategory" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white font-medium">
          <option value="">All Categories</option>
          <option value="professional">Professional</option>
          <option value="academic">Academic</option>
          <option value="hobby">Hobby</option>
          <option value="location">Location</option>
          <option value="batch">Batch</option>
        </select>
        <select v-model="selectedVisibility" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white font-medium">
          <option value="">All Types</option>
          <option value="public">Public</option>
          <option value="private">Private</option>
        </select>
          </div>
        </div>
        
    <!-- Communities Grid -->
    <div v-if="filteredCommunities.length === 0" class="text-center py-12 text-gray-500">
      <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
      </svg>
      <p>No communities found</p>
      <p class="text-sm">Try adjusting your search or filters</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="community in filteredCommunities" 
        :key="community.id"
        class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
      >
        <!-- Community Banner -->
        <div class="h-36 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 relative overflow-hidden">
          <div class="absolute inset-0 bg-black bg-opacity-10"></div>
          <div class="absolute top-4 right-4">
            <span :class="[
              'px-3 py-1 text-xs font-bold rounded-full shadow-sm',
              community.visibility === 'public' ? 'bg-white text-green-600' : 'bg-white text-yellow-600'
            ]">
              {{ community.visibility === 'public' ? '✓ Public' : '🔒 Private' }}
            </span>
        </div>
          <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/40 to-transparent">
            <h3 class="text-xl font-bold text-white mb-1">{{ community.name }}</h3>
            <p class="text-white/90 text-sm font-medium capitalize">{{ community.category || 'General' }}</p>
      </div>
    </div>

        <!-- Community Info -->
        <div class="p-6">
          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ community.description }}</p>
          
          <!-- Tags -->
          <div v-if="community.tags && community.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
            <span 
              v-for="tag in community.tags.slice(0, 3)" 
              :key="tag"
              class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full font-medium border border-blue-200"
            >
              #{{ tag }}
            </span>
            <span v-if="community.tags.length > 3" class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
              +{{ community.tags.length - 3 }} more
            </span>
    </div>

          <!-- Stats -->
          <div class="flex items-center justify-between text-sm mb-4 bg-gray-50 rounded-lg p-3">
            <div class="flex items-center text-gray-700">
              <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
              </div>
              <div>
                <div class="font-bold text-gray-900">{{ community.member_count }}</div>
                <div class="text-xs text-gray-500">members</div>
              </div>
            </div>
            <div class="flex items-center text-gray-700">
              <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-2">
                <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div>
                <div class="font-bold text-gray-900">{{ community.post_count }}</div>
                <div class="text-xs text-gray-500">posts</div>
        </div>
      </div>
    </div>

          <!-- Owner -->
          <div class="flex items-center mb-4 pb-4 border-b border-gray-200">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3 ring-2 ring-white shadow-lg">
              {{ community.owner.first_name[0] }}{{ community.owner.last_name[0] }}
          </div>
          <div class="flex-1">
              <div class="text-sm font-semibold text-gray-900">{{ community.owner.full_name }}</div>
              <div class="text-xs text-gray-500">{{ formatDate(community.created_at) }}</div>
            </div>
          </div>
        
          <!-- Action Buttons -->
          <div class="flex gap-2">
            <button 
              v-if="!community.is_member"
              @click.stop="joinCommunity(community)"
              class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2.5 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 text-sm font-semibold shadow-md transition-all"
            >
              Join Community
            </button>
            <button 
              v-else
              @click.stop="leaveCommunity(community)"
              class="flex-1 bg-gray-100 text-gray-700 py-2.5 px-4 rounded-lg hover:bg-gray-200 text-sm font-semibold border-2 border-gray-200 transition-all"
            >
              ✓ Member
            </button>
            <button 
              @click.stop="viewCommunity(community)"
              class="px-4 py-2.5 border-2 border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold transition-all"
            >
              View Details
            </button>
          </div>
          </div>
        </div>
      </div>

    <!-- Community Detail Modal -->
    <div v-if="showCommunityModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden border-4 border-gray-300 shadow-2xl">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-lg mr-4">
              {{ selectedCommunity.name[0] }}
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-900">{{ selectedCommunity.name }}</h2>
              <p class="text-sm text-gray-600">{{ selectedCommunity.category }} • {{ selectedCommunity.member_count }} members</p>
            </div>
          </div>
          <button @click="showCommunityModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            </button>
          </div>

        <!-- Modal Content -->
        <div class="flex h-[calc(90vh-120px)]">
          <!-- Left Panel - Community Info -->
          <div class="w-1/3 border-r border-gray-200 p-6 overflow-y-auto">
            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">About</h3>
              <p class="text-gray-600 text-sm">{{ selectedCommunity.description }}</p>
            </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Tags</h3>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="tag in selectedCommunity.tags" 
                  :key="tag"
                  class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
                >
                  {{ tag }}
                </span>
      </div>
    </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Rules</h3>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>• Be respectful and inclusive</li>
                <li>• Stay on topic</li>
                <li>• No spam or self-promotion</li>
                <li>• Follow community guidelines</li>
              </ul>
    </div>

            <div class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Recent Members</h3>
              <div class="space-y-3">
                <div 
                  v-for="member in selectedCommunity.recent_members" 
                  :key="member.id"
                  class="flex items-center"
                >
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs mr-3">
                    {{ member.first_name[0] }}{{ member.last_name[0] }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ member.full_name }}</div>
                    <div class="text-xs text-gray-500">{{ member.batch }} • {{ member.role }}</div>
                  </div>
                </div>
          </div>
        </div>
      </div>

          <!-- Right Panel - Posts and Discussions -->
          <div class="flex-1 p-6 overflow-y-auto">
            <div class="mb-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Posts</h3>
                <button 
                  @click="showCreatePostModal = true"
                  class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm"
                >
                  Create Post
                </button>
              </div>

              <!-- Post Composer -->
              <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    CU
          </div>
          <div class="flex-1">
                    <textarea 
                      v-model="newPostContent"
                      placeholder="Share something with the community..."
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    ></textarea>
                    <div class="flex items-center justify-between mt-2">
                      <div class="flex items-center space-x-2">
                        <button class="text-gray-500 hover:text-gray-700">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                          </svg>
                        </button>
                        <button class="text-gray-500 hover:text-gray-700">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                        </button>
                      </div>
                      <button 
                        @click="createPost"
                        :disabled="!newPostContent.trim()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm"
                      >
                        Post
            </button>
                    </div>
          </div>
        </div>
      </div>

              <!-- Posts List -->
              <div class="space-y-6">
                <div 
                  v-for="post in selectedCommunity.posts" 
                  :key="post.id"
                  class="bg-white border border-gray-200 rounded-lg p-6"
                >
        <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                      {{ post.author.first_name[0] }}{{ post.author.last_name[0] }}
          </div>
          <div class="flex-1">
                      <div class="flex items-center justify-between">
                        <div>
                          <h4 class="text-sm font-semibold text-gray-900">{{ post.author.full_name }}</h4>
                          <p class="text-xs text-gray-500">{{ formatDate(post.created_at) }}</p>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                          </svg>
            </button>
          </div>
        </div>
      </div>

                  <div class="mb-4">
                    <p class="text-gray-800">{{ post.content }}</p>
          </div>

                  <div class="flex items-center justify-between text-sm text-gray-500">
                    <div class="flex items-center space-x-4">
                      <button class="flex items-center hover:text-blue-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        {{ post.likes_count }}
                      </button>
                      <button class="flex items-center hover:text-blue-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        {{ post.comments_count }}
                      </button>
                      <button class="flex items-center hover:text-blue-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Share
            </button>
          </div>
        </div>
      </div>
              </div>
          </div>
          </div>
          </div>
        </div>
      </div>

    <!-- Create Community Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 w-full max-w-md border-4 border-gray-300 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Create Community</h3>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            </button>
          </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Community Name</label>
            <input 
              v-model="newCommunity.name"
              type="text"
              placeholder="Enter community name..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="newCommunity.description"
              rows="3"
              placeholder="Describe your community..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select v-model="newCommunity.category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
              <option value="">Select category...</option>
              <option value="professional">Professional</option>
              <option value="academic">Academic</option>
              <option value="hobby">Hobby</option>
              <option value="location">Location</option>
              <option value="batch">Batch</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Visibility</label>
            <div class="flex space-x-4">
              <label class="flex items-center">
                <input type="radio" v-model="newCommunity.visibility" value="public" class="mr-2">
                <span class="text-sm text-gray-700">Public</span>
              </label>
              <label class="flex items-center">
                <input type="radio" v-model="newCommunity.visibility" value="private" class="mr-2">
                <span class="text-sm text-gray-700">Private</span>
              </label>
        </div>
      </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tags (comma-separated)</label>
            <input 
              v-model="newCommunity.tags"
              type="text"
              placeholder="e.g., technology, career, networking"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          </div>
        
        <div class="flex justify-end space-x-2 mt-6">
          <button @click="showCreateModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button 
            @click="createCommunity" 
            :disabled="!newCommunity.name || !newCommunity.description"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            Create Community
            </button>
          </div>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../config/api'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()

// Reactive data
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedVisibility = ref('')
const showCommunityModal = ref(false)
const showCreateModal = ref(false)
const showCreatePostModal = ref(false)
const selectedCommunity = ref({})
const newPostContent = ref('')
const currentUser = computed(() => authStore.user)

const newCommunity = ref({
  name: '',
  description: '',
  category: '',
  visibility: 'public',
  tags: ''
})

// Fetch communities from API
const communities = ref([])
const loading = ref(false)

const fetchCommunities = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value || undefined,
      category: selectedCategory.value || undefined,
      visibility: selectedVisibility.value || undefined,
    }
    
    const response = await axios.get('/api/communities', { params })
    if (response?.data?.success && Array.isArray(response.data.data)) {
      communities.value = response.data.data.map(community => ({
        id: community.id,
        name: community.name,
        description: community.description,
        category: community.category || 'professional',
        visibility: community.visibility || 'public',
        tags: community.tags ? (typeof community.tags === 'string' ? JSON.parse(community.tags) : community.tags) : [],
        member_count: community.members ? community.members.length : 0,
        post_count: 0, // Will be updated when posts are implemented
        created_at: new Date(community.created_at),
        is_member: community.members ? community.members.some(member => member.id === currentUser.value?.id) : false,
        owner: community.creator ? {
          first_name: community.creator.first_name || 'Unknown',
          last_name: community.creator.last_name || 'User',
          full_name: `${community.creator.first_name || 'Unknown'} ${community.creator.last_name || 'User'}`
        } : { first_name: 'Unknown', last_name: 'User', full_name: 'Unknown User' },
        recent_members: community.members && community.members.length > 0 ? community.members.slice(0, 3).map(member => ({
          id: member.id,
          first_name: member.first_name || '',
          last_name: member.last_name || '',
          full_name: `${member.first_name || ''} ${member.last_name || ''}`,
          batch: member.batch || 'Unknown',
          role: member.current_job_title || 'Alumni'
        })) : [],
        posts: [] // Will be implemented later
      }))
    } else {
      communities.value = []
    }
  } catch (error) {
    console.error('Error fetching communities:', error)
    communities.value = []
  } finally {
    loading.value = false
  }
}

// Computed properties
const filteredCommunities = computed(() => {
  let filtered = communities.value

  // Apply search filter
  if (searchQuery.value) {
    filtered = filtered.filter(community => 
      community.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      community.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      community.tags.some(tag => tag.toLowerCase().includes(searchQuery.value.toLowerCase()))
    )
  }

  // Apply category filter
  if (selectedCategory.value) {
    filtered = filtered.filter(community => community.category === selectedCategory.value)
  }

  // Apply visibility filter
  if (selectedVisibility.value) {
    filtered = filtered.filter(community => community.visibility === selectedVisibility.value)
  }

  return filtered
})

// Methods
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const viewCommunity = (community) => {
  selectedCommunity.value = community
  showCommunityModal.value = true
}

const joinCommunity = async (community) => {
  try {
    const response = await axios.post(`/api/communities/${community.id}/join`)
    if (response?.data?.success) {
      community.is_member = true
      community.member_count++
    }
  } catch (error) {
    console.error('Error joining community:', error)
  }
}

const leaveCommunity = async (community) => {
  try {
    const response = await axios.post(`/api/communities/${community.id}/leave`)
    if (response?.data?.success) {
      community.is_member = false
      community.member_count--
    }
  } catch (error) {
    console.error('Error leaving community:', error)
  }
}

const createCommunity = async () => {
  if (!newCommunity.value.name || !newCommunity.value.description) return
  try {
    const payload = {
      name: newCommunity.value.name,
      description: newCommunity.value.description,
      category: newCommunity.value.category || null,
      visibility: newCommunity.value.visibility || 'public',
      tags: newCommunity.value.tags
        ? newCommunity.value.tags.split(',').map(t => t.trim()).filter(Boolean)
        : []
    }
    const response = await axios.post('/api/communities', payload)
    if (response?.data?.success) {
      showCreateModal.value = false
      newCommunity.value = { name: '', description: '', category: '', visibility: 'public', tags: '' }
      await fetchCommunities()
    }
  } catch (error) {
    console.error('Error creating community:', error)
  }
}

const createPost = async () => {
  if (!newPostContent.value.trim()) return
  try {
    const response = await axios.post('/api/posts', { content: newPostContent.value })
    if (response?.data?.success) {
      const p = response.data.data
      const post = {
        id: p.post_id || Date.now(),
        content: p.content,
        author: {
          first_name: authStore.user?.first_name || 'Current',
          last_name: authStore.user?.last_name || 'User',
          full_name: `${authStore.user?.first_name || 'Current'} ${authStore.user?.last_name || 'User'}`
        },
        created_at: p.created_at || new Date(),
        likes_count: 0,
        comments_count: 0
      }
      selectedCommunity.value.posts.unshift(post)
      selectedCommunity.value.post_count++
      newPostContent.value = ''
    }
  } catch (error) {
    console.error('Error creating post:', error)
  }
}

onMounted(() => {
  fetchCommunities()
})

// Watch for filter changes and refetch
watch([searchQuery, selectedCategory, selectedVisibility], () => {
  fetchCommunities()
})
</script>