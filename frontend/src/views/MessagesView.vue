<template>
  <div class="flex h-full bg-gray-100">
    <!-- Left Panel - Conversations List -->
    <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
      <!-- Header -->
      <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-bold text-gray-800">Messages</h2>
          <button 
            @click="showNewMessageModal = true"
            class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2"
          >
            <span class="text-lg">+</span>
            New Message
            </button>
          </div>
        
        <!-- Search Bar -->
          <div class="relative">
            <input 
            v-model="searchQuery"
              type="text" 
            placeholder="Search messages or alumni..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          </div>
        </div>
        
      <!-- Filters/Tabs -->
      <div class="px-4 py-2 border-b border-gray-200">
        <div class="flex space-x-4">
          <button 
            v-for="filter in messageFilters" 
            :key="filter.key"
            @click="activeFilter = filter.key"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-colors',
              activeFilter === filter.key 
                ? 'bg-blue-100 text-blue-700' 
                : 'text-gray-600 hover:text-gray-800'
            ]"
          >
            {{ filter.label }}
            <span v-if="filter.count" class="ml-1 bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full text-xs">
              {{ filter.count }}
            </span>
          </button>
        </div>
      </div>

      <!-- Conversations List -->
      <div class="flex-1 overflow-y-auto">
        <div v-if="filteredConversations.length === 0" class="p-8 text-center text-gray-500">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
          <p>No messages yet</p>
          <p class="text-sm">Connect with alumni to start a conversation.</p>
        </div>
        
        <div v-else class="space-y-1">
          <div 
            v-for="conversation in filteredConversations" 
            :key="conversation.id"
            @click="selectConversation(conversation)"
            :class="[
              'flex items-center p-4 hover:bg-gray-50 cursor-pointer border-l-4 transition-colors',
              selectedConversation?.id === conversation.id 
                ? 'border-blue-500 bg-blue-50' 
                : 'border-transparent'
            ]"
          >
            <div class="relative">
              <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                {{ conversation.user.first_name[0] }}{{ conversation.user.last_name[0] }}
              </div>
              <div v-if="conversation.unread_count > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ conversation.unread_count }}
      </div>
    </div>

            <div class="ml-3 flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 truncate">
                  {{ conversation.user.full_name }}
                </h3>
                <span class="text-xs text-gray-500">
                  {{ formatTime(conversation.last_message?.sent_at) }}
                </span>
              </div>
              <p class="text-sm text-gray-600 truncate">
                {{ conversation.last_message?.content || 'No messages yet' }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Middle Panel - Chat Window -->
    <div class="flex-1 flex flex-col">
      <div v-if="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50">
        <div class="text-center text-gray-500">
          <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
          <p class="text-sm">Choose a conversation from the list to start messaging.</p>
        </div>
      </div>

      <div v-else class="flex-1 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-white border-b border-gray-200 p-4">
              <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                {{ selectedConversation.user.first_name[0] }}{{ selectedConversation.user.last_name[0] }}
              </div>
              <div>
                <h3 class="font-semibold text-gray-900">{{ selectedConversation.user.full_name }}</h3>
                <p class="text-sm text-gray-500">{{ selectedConversation.user.headline || 'Alumni' }}</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-2">
              <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
          </button>
              <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                </svg>
          </button>
        </div>
      </div>
    </div>

        <!-- Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
          <div v-if="conversationMessages.length === 0" class="text-center text-gray-500 py-8">
            <p>No messages yet. Start the conversation!</p>
          </div>
          
          <div v-for="message in conversationMessages" :key="message.id" class="flex" :class="message.sender_id === currentUserId ? 'justify-end' : 'justify-start'">
            <div :class="[
              'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
              message.sender_id === currentUserId 
                ? 'bg-blue-600 text-white' 
                : 'bg-white text-gray-900 border border-gray-200'
            ]">
              <p class="text-sm">{{ message.content }}</p>
              <p :class="[
                'text-xs mt-1',
                message.sender_id === currentUserId ? 'text-blue-100' : 'text-gray-500'
              ]">
                {{ formatTime(message.sent_at) }}
              </p>
          </div>
        </div>
          
          <div v-if="isTyping" class="flex justify-start">
            <div class="bg-white border border-gray-200 rounded-lg px-4 py-2">
              <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              </div>
        </div>
              </div>
            </div>

        <!-- Message Composer -->
        <div class="bg-white border-t border-gray-200 p-4">
          <div class="flex items-center space-x-2">
            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
              </svg>
          </button>
            
            <div class="flex-1">
              <input
                v-model="newMessage"
                @keypress.enter="sendMessage"
                type="text"
                placeholder="Type a message..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              </div>
            
            <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
          </button>
            
            <button 
              @click="sendMessage"
              :disabled="!newMessage.trim()"
              class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Send
          </button>
        </div>
        </div>
      </div>
    </div>

    <!-- Right Panel - Profile & Context -->
    <div v-if="selectedConversation" class="w-1/4 bg-white border-l border-gray-200 p-4">
      <!-- Profile Card -->
      <div class="text-center mb-6">
        <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl mx-auto mb-3">
          {{ selectedConversation.user.first_name[0] }}{{ selectedConversation.user.last_name[0] }}
        </div>
        <h3 class="font-semibold text-gray-900">{{ selectedConversation.user.full_name }}</h3>
        <p class="text-sm text-gray-500">{{ selectedConversation.user.headline || 'Alumni' }}</p>
        <p class="text-sm text-gray-500">{{ selectedConversation.user.bio || 'No bio available' }}</p>
      </div>

      <!-- Quick Actions -->
      <div class="space-y-2 mb-6">
        <button class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
          View Full Profile
        </button>
        <button class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50">
          Request Mentorship
        </button>
        <button class="w-full border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-50">
          Recommend for Job
        </button>
      </div>

      <!-- Mutual Connections -->
      <div class="mb-6">
        <h4 class="font-semibold text-gray-900 mb-3">Mutual Connections</h4>
        <div class="space-y-2">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
              AC
            </div>
            <span class="text-sm text-gray-700">Alex Chen</span>
          </div>
          <div class="flex items-center">
            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
              SM
            </div>
            <span class="text-sm text-gray-700">Sarah Martinez</span>
            </div>
          </div>
        </div>

      <!-- Recent Activity -->
          <div>
        <h4 class="font-semibold text-gray-900 mb-3">Recent Activity</h4>
        <div class="space-y-3">
          <div class="text-sm">
            <p class="text-gray-700">Posted a job at Tech Corp</p>
            <p class="text-gray-500 text-xs">2 days ago</p>
          </div>
          <div class="text-sm">
            <p class="text-gray-700">Attended Alumni Meetup</p>
            <p class="text-gray-500 text-xs">1 week ago</p>
          </div>
        </div>
      </div>
      </div>

    <!-- New Message Modal -->
    <div v-if="showNewMessageModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md border-4 border-gray-300 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">New Message</h3>
          <button @click="showNewMessageModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Select Recipient</label>
          <select v-model="newMessageRecipient" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Choose an alumni...</option>
            <option v-for="user in availableUsers" :key="user.id" :value="user.id">
              {{ user.full_name }} - {{ user.headline || 'Alumni' }}
            </option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
          <textarea 
            v-model="newMessageContent"
            rows="3"
            placeholder="Type your message..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
      </div>

        <div class="flex justify-end space-x-2">
          <button @click="showNewMessageModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="sendNewMessage" :disabled="!newMessageRecipient || !newMessageContent.trim()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            Send Message
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
const activeFilter = ref('all')
const selectedConversation = ref(null)
const conversationMessages = ref([])
const newMessage = ref('')
const isTyping = ref(false)
const showNewMessageModal = ref(false)
const newMessageRecipient = ref('')
const newMessageContent = ref('')
const currentUserId = ref(1) // This would come from auth

// Sample data - replace with API calls
const conversations = ref([
  {
    id: 1,
    user: {
      id: 2,
      first_name: 'John',
      last_name: 'Doe',
      full_name: 'John Doe',
      headline: 'Software Engineer at Google',
      bio: 'Passionate about technology and helping others grow in their careers.'
    },
    last_message: {
      id: 1,
      content: 'Hey! How\'s the job search going?',
      sent_at: new Date(Date.now() - 2 * 60 * 1000) // 2 minutes ago
    },
    unread_count: 2
  },
  {
    id: 2,
    user: {
      id: 3,
      first_name: 'Jane',
      last_name: 'Smith',
      full_name: 'Jane Smith',
      headline: 'Senior Developer at Microsoft',
      bio: 'Experienced mentor and tech enthusiast.'
    },
    last_message: {
      id: 2,
      content: 'Thanks for the mentorship session!',
      sent_at: new Date(Date.now() - 60 * 60 * 1000) // 1 hour ago
    },
    unread_count: 0
  },
  {
    id: 3,
    user: {
      id: 4,
      first_name: 'Mike',
      last_name: 'Johnson',
      full_name: 'Mike Johnson',
      headline: 'Product Manager at Amazon',
      bio: 'Building products that matter.'
    },
    last_message: {
      id: 3,
      content: 'Let\'s connect for the upcoming event!',
      sent_at: new Date(Date.now() - 3 * 60 * 60 * 1000) // 3 hours ago
    },
    unread_count: 1
  }
])

const availableUsers = ref([
  { id: 5, first_name: 'Alice', last_name: 'Brown', full_name: 'Alice Brown', headline: 'UX Designer' },
  { id: 6, first_name: 'Bob', last_name: 'Wilson', full_name: 'Bob Wilson', headline: 'Data Scientist' },
  { id: 7, first_name: 'Carol', last_name: 'Davis', full_name: 'Carol Davis', headline: 'Marketing Manager' }
])

// Computed properties
const messageFilters = computed(() => [
  { key: 'all', label: 'All', count: conversations.value.length },
  { key: 'unread', label: 'Unread', count: conversations.value.reduce((sum, conv) => sum + conv.unread_count, 0) },
  { key: 'starred', label: 'Starred', count: 0 },
  { key: 'archived', label: 'Archived', count: 0 }
])

const filteredConversations = computed(() => {
  let filtered = conversations.value

  // Apply search filter
  if (searchQuery.value) {
    filtered = filtered.filter(conv => 
      conv.user.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      conv.last_message?.content.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Apply active filter
  switch (activeFilter.value) {
    case 'unread':
      filtered = filtered.filter(conv => conv.unread_count > 0)
      break
    case 'starred':
      // Implement starred logic
      break
    case 'archived':
      // Implement archived logic
      break
  }

  return filtered
})

// Methods
const selectConversation = (conversation) => {
  selectedConversation.value = conversation
  // Load messages for this conversation
  loadMessages(conversation.id)
}

const loadMessages = (conversationId) => {
  // Sample messages - replace with API call
  conversationMessages.value = [
    {
      id: 1,
      sender_id: 2,
      receiver_id: 1,
      content: 'Hey! How\'s the job search going?',
      sent_at: new Date(Date.now() - 2 * 60 * 1000),
      is_read: false
    },
    {
      id: 2,
      sender_id: 1,
      receiver_id: 2,
      content: 'It\'s going well! I have a few interviews lined up.',
      sent_at: new Date(Date.now() - 1 * 60 * 1000),
      is_read: true
    },
    {
      id: 3,
      sender_id: 2,
      receiver_id: 1,
      content: 'That\'s great! Let me know if you need any preparation tips.',
      sent_at: new Date(Date.now() - 30 * 1000),
      is_read: false
    }
  ]
}

const sendMessage = () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return

  const message = {
    id: Date.now(),
    sender_id: currentUserId.value,
    receiver_id: selectedConversation.value.user.id,
    content: newMessage.value,
    sent_at: new Date(),
    is_read: false
  }

  conversationMessages.value.push(message)
  
  // Update last message in conversation
  selectedConversation.value.last_message = message
  
  newMessage.value = ''
  
  // Simulate typing indicator
  isTyping.value = true
  setTimeout(() => {
    isTyping.value = false
  }, 1000)
}

const sendNewMessage = () => {
  if (!newMessageRecipient.value || !newMessageContent.value.trim()) return

  // Create new conversation
  const recipient = availableUsers.value.find(u => u.id === newMessageRecipient.value)
  const newConversation = {
    id: Date.now(),
    user: recipient,
    last_message: {
      id: Date.now(),
      content: newMessageContent.value,
      sent_at: new Date()
    },
    unread_count: 0
  }

  conversations.value.unshift(newConversation)
  
  // Select the new conversation
  selectConversation(newConversation)
  
  // Reset modal
  showNewMessageModal.value = false
  newMessageRecipient.value = ''
  newMessageContent.value = ''
}

const formatTime = (date) => {
  if (!date) return ''
  
  const now = new Date()
  const diff = now - new Date(date)
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)

  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  // Load initial data
})
</script>