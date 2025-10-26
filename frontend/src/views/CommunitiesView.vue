<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Communities</h1>
        <p class="text-gray-600 mt-1">Connect with alumni through shared interests</p>
      </div>
      <button 
        @click="showCreateModal = true"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
      >
        Create Community
            </button>
          </div>

    <!-- Search and Filters -->
    <div class="flex items-center gap-4 mb-6">
      <div class="flex-1">
            <input 
          v-model="searchQuery"
              type="text" 
          placeholder="Search communities by name, description, or tags..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>
      <select v-model="selectedCategory" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="">All Categories</option>
        <option value="professional">Professional</option>
        <option value="academic">Academic</option>
        <option value="hobby">Hobby</option>
        <option value="location">Location</option>
        <option value="batch">Batch</option>
      </select>
      <select v-model="selectedVisibility" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        <option value="">All Types</option>
        <option value="public">Public</option>
        <option value="private">Private</option>
      </select>
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
        class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow cursor-pointer"
        @click="viewCommunity(community)"
      >
        <!-- Community Banner -->
        <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
          <div class="absolute inset-0 bg-black bg-opacity-20"></div>
          <div class="absolute bottom-4 left-4">
            <h3 class="text-xl font-bold text-white">{{ community.name }}</h3>
            <p class="text-sm text-blue-100">{{ community.category }}</p>
          </div>
          <div class="absolute top-4 right-4">
            <span :class="[
              'px-2 py-1 text-xs font-semibold rounded-full',
              community.visibility === 'public' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
            ]">
              {{ community.visibility }}
            </span>
          </div>
        </div>
        
        <!-- Community Info -->
        <div class="p-6">
          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ community.description }}</p>
          
          <!-- Tags -->
          <div class="flex flex-wrap gap-2 mb-4">
            <span 
              v-for="tag in community.tags.slice(0, 3)" 
              :key="tag"
              class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full"
            >
              {{ tag }}
            </span>
            <span v-if="community.tags.length > 3" class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
              +{{ community.tags.length - 3 }} more
            </span>
          </div>

          <!-- Stats -->
          <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              {{ community.member_count }} members
            </div>
            <div class="flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              {{ community.post_count }} posts
            </div>
          </div>

          <!-- Owner -->
          <div class="flex items-center mb-4">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs mr-2">
              {{ community.owner.first_name[0] }}{{ community.owner.last_name[0] }}
            </div>
            <div>
              <div class="text-sm font-medium text-gray-900">{{ community.owner.full_name }}</div>
              <div class="text-xs text-gray-500">Created {{ formatDate(community.created_at) }}</div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2">
            <button 
              v-if="!community.is_member"
              @click.stop="joinCommunity(community)"
              class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 text-sm"
            >
              Join Community
          </button>
            <button 
              v-else
              @click.stop="leaveCommunity(community)"
              class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50 text-sm"
            >
              Leave Community
          </button>
            <button 
              @click.stop="viewCommunity(community)"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm"
            >
              View
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
import { ref, computed, onMounted } from 'vue'

// Reactive data
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedVisibility = ref('')
const showCommunityModal = ref(false)
const showCreateModal = ref(false)
const showCreatePostModal = ref(false)
const selectedCommunity = ref({})
const newPostContent = ref('')

const newCommunity = ref({
  name: '',
  description: '',
  category: '',
  visibility: 'public',
  tags: ''
})

// Sample data - replace with API calls
const communities = ref([
  {
    id: 1,
    name: 'LCBA Tech Professionals',
    description: 'A community for LCBA alumni working in technology and software development. Share experiences, job opportunities, and technical discussions.',
    category: 'professional',
    visibility: 'public',
    tags: ['technology', 'software', 'career', 'networking'],
    member_count: 156,
    post_count: 89,
    created_at: new Date('2024-01-15'),
    is_member: true,
    owner: {
      first_name: 'Ryan',
      last_name: 'Ebrada',
      full_name: 'Ryan Ebrada'
    },
    recent_members: [
      { id: 1, first_name: 'John', last_name: 'Cruz', full_name: 'John Cruz', batch: '2020', role: 'Software Engineer' },
      { id: 2, first_name: 'Sarah', last_name: 'Martinez', full_name: 'Sarah Martinez', batch: '2019', role: 'Product Manager' },
      { id: 3, first_name: 'Mike', last_name: 'Johnson', full_name: 'Mike Johnson', batch: '2021', role: 'Data Scientist' }
    ],
    posts: [
      {
        id: 1,
        content: 'Just landed a new role as Senior Software Engineer at Google! Thanks to everyone who helped with interview prep.',
        author: { first_name: 'John', last_name: 'Cruz', full_name: 'John Cruz' },
        created_at: new Date('2025-08-20'),
        likes_count: 24,
        comments_count: 8
      },
      {
        id: 2,
        content: 'Looking for recommendations on good online courses for machine learning. Any suggestions?',
        author: { first_name: 'Sarah', last_name: 'Martinez', full_name: 'Sarah Martinez' },
        created_at: new Date('2025-08-19'),
        likes_count: 12,
        comments_count: 15
      }
    ]
  },
  {
    id: 2,
    name: 'LCBA Entrepreneurs',
    description: 'Connect with fellow LCBA alumni who are building their own businesses and startups. Share resources, advice, and success stories.',
    category: 'professional',
    visibility: 'public',
    tags: ['entrepreneurship', 'startup', 'business', 'innovation'],
    member_count: 89,
    post_count: 45,
    created_at: new Date('2024-03-10'),
    is_member: false,
    owner: {
      first_name: 'Alice',
      last_name: 'Lee',
      full_name: 'Alice Lee'
    },
    recent_members: [
      { id: 4, first_name: 'David', last_name: 'Kim', full_name: 'David Kim', batch: '2018', role: 'Founder' },
      { id: 5, first_name: 'Emma', last_name: 'Wilson', full_name: 'Emma Wilson', batch: '2020', role: 'CEO' }
    ],
    posts: []
  },
  {
    id: 3,
    name: 'LCBA Manila Chapter',
    description: 'Alumni based in Manila and nearby areas. Organize meetups, networking events, and local activities.',
    category: 'location',
    visibility: 'public',
    tags: ['manila', 'networking', 'events', 'local'],
    member_count: 234,
    post_count: 67,
    created_at: new Date('2024-02-20'),
    is_member: true,
    owner: {
      first_name: 'Maria',
      last_name: 'Santos',
      full_name: 'Maria Santos'
    },
    recent_members: [
      { id: 6, first_name: 'Carlos', last_name: 'Reyes', full_name: 'Carlos Reyes', batch: '2019', role: 'Marketing Manager' },
      { id: 7, first_name: 'Ana', last_name: 'Garcia', full_name: 'Ana Garcia', batch: '2021', role: 'UX Designer' }
    ],
    posts: []
  },
  {
    id: 4,
    name: 'LCBA Batch 2020',
    description: 'Exclusive community for LCBA Batch 2020 graduates. Stay connected with your batchmates and share memories.',
    category: 'batch',
    visibility: 'private',
    tags: ['batch-2020', 'memories', 'friendship', 'reunion'],
    member_count: 45,
    post_count: 23,
    created_at: new Date('2024-01-01'),
    is_member: true,
    owner: {
      first_name: 'Batch',
      last_name: 'Rep',
      full_name: 'Batch Rep'
    },
    recent_members: [
      { id: 8, first_name: 'Luis', last_name: 'Fernandez', full_name: 'Luis Fernandez', batch: '2020', role: 'Developer' },
      { id: 9, first_name: 'Sofia', last_name: 'Lopez', full_name: 'Sofia Lopez', batch: '2020', role: 'Designer' }
    ],
    posts: []
  },
  {
    id: 5,
    name: 'LCBA Photography Club',
    description: 'For photography enthusiasts among LCBA alumni. Share photos, techniques, and organize photo walks.',
    category: 'hobby',
    visibility: 'public',
    tags: ['photography', 'art', 'creative', 'hobby'],
    member_count: 67,
    post_count: 34,
    created_at: new Date('2024-04-05'),
    is_member: false,
    owner: {
      first_name: 'Photo',
      last_name: 'Club',
      full_name: 'Photo Club'
    },
    recent_members: [
      { id: 10, first_name: 'Roberto', last_name: 'Silva', full_name: 'Roberto Silva', batch: '2019', role: 'Photographer' },
      { id: 11, first_name: 'Isabella', last_name: 'Torres', full_name: 'Isabella Torres', batch: '2021', role: 'Graphic Designer' }
    ],
    posts: []
  }
])

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

const joinCommunity = (community) => {
  community.is_member = true
  community.member_count++
  console.log('Joined community:', community.name)
}

const leaveCommunity = (community) => {
  community.is_member = false
  community.member_count--
  console.log('Left community:', community.name)
}

const createCommunity = () => {
  if (!newCommunity.value.name || !newCommunity.value.description) return

  const tags = newCommunity.value.tags.split(',').map(tag => tag.trim()).filter(tag => tag)
  
  const community = {
    id: communities.value.length + 1,
    name: newCommunity.value.name,
    description: newCommunity.value.description,
    category: newCommunity.value.category,
    visibility: newCommunity.value.visibility,
    tags: tags,
    member_count: 1,
    post_count: 0,
    created_at: new Date(),
    is_member: true,
    owner: {
      first_name: 'Current',
      last_name: 'User',
      full_name: 'Current User'
    },
    recent_members: [],
    posts: []
  }

  communities.value.unshift(community)
  showCreateModal.value = false

  // Reset form
  newCommunity.value = {
    name: '',
    description: '',
    category: '',
    visibility: 'public',
    tags: ''
  }
}

const createPost = () => {
  if (!newPostContent.value.trim()) return

  const post = {
    id: Date.now(),
    content: newPostContent.value,
    author: {
      first_name: 'Current',
      last_name: 'User',
      full_name: 'Current User'
    },
    created_at: new Date(),
    likes_count: 0,
    comments_count: 0
  }

  selectedCommunity.value.posts.unshift(post)
  selectedCommunity.value.post_count++
  newPostContent.value = ''
}

onMounted(() => {
  // Load initial data
})
</script>