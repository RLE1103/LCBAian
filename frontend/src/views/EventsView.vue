<template>
  <div class="p-4 md:p-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 md:mb-6 gap-3">
      <div>
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Alumni Events</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Discover and join upcoming alumni events</p>
      </div>
      <div class="flex items-center space-x-2 md:space-x-4">
        <button 
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-3 md:px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2 text-sm md:text-base"
        >
          <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Create Event
        </button>
        <button 
          @click="viewMode = viewMode === 'list' ? 'calendar' : 'list'"
          class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          {{ viewMode === 'list' ? 'View Calendar' : 'View Events' }}
            </button>
          </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="flex flex-col lg:flex-row gap-4">
        <!-- Search Bar -->
        <div class="flex-1">
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search events by name, date, or type..." 
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
        </div>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-2">
          <select v-model="selectedEventType" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Event Types</option>
            <option value="webinar">Webinar</option>
            <option value="reunion">Reunion</option>
            <option value="career-fair">Career Fair</option>
            <option value="workshop">Workshop</option>
            <option value="talk">Talk</option>
            <option value="seminar">Seminar</option>
          </select>

          <select v-model="selectedLocation" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Locations</option>
            <option value="on-campus">On-campus</option>
            <option value="off-campus">Off-campus</option>
            <option value="online">Online</option>
          </select>

          <select v-model="selectedDateRange" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">All Dates</option>
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="past">Past</option>
          </select>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - Event List/Calendar -->
      <div class="lg:col-span-2">
        <!-- Calendar View -->
        <div v-if="viewMode === 'calendar'" class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ currentMonth }} {{ currentYear }}</h2>
            <div class="flex items-center space-x-2">
              <button @click="previousMonth" class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
              <button @click="nextMonth" class="p-2 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Calendar Grid -->
          <div class="grid grid-cols-7 gap-1">
            <div class="p-2 text-center text-sm font-medium text-gray-500">Sun</div>
            <div class="p-2 text-center text-sm font-medium text-gray-500">Mon</div>
            <div class="p-2 text-center text-sm font-medium text-gray-500">Tue</div>
            <div class="p-2 text-center text-sm font-medium text-gray-500">Wed</div>
            <div class="p-2 text-center text-sm font-medium text-gray-500">Thu</div>
            <div class="p-2 text-center text-sm font-medium text-gray-500">Fri</div>
            <div class="p-2 text-center text-sm font-medium text-gray-500">Sat</div>
            
            <div 
              v-for="day in calendarDays" 
              :key="day.date"
              @click="selectDate(day)"
              :class="[
                'p-2 text-center text-sm cursor-pointer rounded-lg hover:bg-gray-100',
                day.isCurrentMonth ? 'text-gray-900' : 'text-gray-400',
                day.isToday ? 'bg-blue-100 text-blue-600' : '',
                day.hasEvents ? 'font-semibold' : '',
                day.isSelected ? 'bg-blue-600 text-white hover:bg-blue-600' : ''
              ]"
            >
              {{ day.day }}
              <div v-if="day.hasEvents" class="w-2 h-2 bg-blue-500 rounded-full mx-auto mt-1"></div>
                </div>
              </div>
              <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                  <h3 class="text-sm font-semibold text-gray-900">Events on {{ selectedDateLabel }}</h3>
                  <span class="text-xs text-gray-500">{{ selectedDateEvents.length }} total</span>
                </div>
                <div v-if="selectedDateEvents.length === 0" class="text-sm text-gray-500">
                  No events scheduled
                </div>
                <div v-else class="space-y-3">
                  <div
                    v-for="event in selectedDateEvents"
                    :key="event.id"
                    class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer"
                    @click="viewEvent(event)"
                  >
                    <div class="flex items-start justify-between">
                      <div>
                        <h4 class="text-sm font-medium text-gray-900">{{ event.title }}</h4>
                        <p class="text-xs text-gray-600">{{ formatTime(event.start_date) }} - {{ formatTime(event.end_date) }}</p>
                      </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 flex items-center gap-2">
                      <span>{{ event.location }}</span>
                      <span v-if="event.user_rsvp === 'going'" class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full">
                        Going
                      </span>
                    </span>
                    <div v-if="isAdmin" class="relative">
                      <button @click.stop="toggleEventMenu(event)" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100" title="Moderate">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                      </button>
                      <div v-if="activeEventMenuId === event.id" class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                        <button @click.stop="openModerationConfirm('remove_content', event)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Remove Content
                        </button>
                        <button @click.stop="openModerationConfirm('suspend_user', event)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Suspend User
                        </button>
                        <button @click.stop="openModerationConfirm('issue_warning', event)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Issue Warning
                        </button>
                      </div>
                    </div>
                  </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        <!-- List View -->
        <div v-else class="space-y-4">
          <div v-if="loading" class="text-center py-12 text-gray-500">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p>Loading events...</p>
          </div>
          <div v-else-if="filteredEvents.length === 0" class="text-center py-12 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p>No events found</p>
            <p class="text-sm">Try adjusting your search or filters</p>
          </div>

          <div 
            v-for="event in paginatedEvents" 
            :key="event.id"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer"
            @click="viewEvent(event)"
          >
              <div class="flex items-start gap-4">
              <!-- Event Banner/Icon -->
              <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                {{ getEventIcon(event.type) }}
                </div>
              
                <div class="flex-1">
                <div class="flex items-start justify-between mb-2">
                    <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ event.title }}</h3>
                    <p class="text-sm text-gray-600">Hosted by {{ event.creator.full_name }}</p>
                    </div>
                  <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">{{ formatTime(event.created_at) }}</span>
                    <button
                      v-if="isAdmin"
                      @click.stop="toggleFeatured(event)"
                      class="text-yellow-500 hover:text-yellow-600"
                      :aria-label="event.is_featured ? 'Unfeature event' : 'Feature event'"
                      title="Toggle featured"
                    >
                      <svg v-if="event.is_featured" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                      </svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l2.072 6.39a1 1 0 00.95.69h6.714c.969 0 1.371 1.24.588 1.81l-5.43 3.95a1 1 0 00-.364 1.118l2.072 6.39c.3.921-.755 1.688-1.54 1.118l-5.43-3.95a1 1 0 00-1.176 0l-5.43 3.95c-.784.57-1.838-.197-1.539-1.118l2.072-6.39a1 1 0 00-.364-1.118l-5.43-3.95c-.783-.57-.38-1.81.588-1.81h6.714a1 1 0 00.95-.69l2.072-6.39z"></path>
                      </svg>
                    </button>
                    <div v-if="isAdmin" class="relative">
                      <button @click.stop="toggleEventMenu(event)" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100" title="Moderate">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4zm0 7a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                      </button>
                      <div v-if="activeEventMenuId === event.id" class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                        <button @click.stop="openModerationConfirm('remove_content', event)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Remove Content
                        </button>
                        <button @click.stop="openModerationConfirm('suspend_user', event)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Suspend User
                        </button>
                        <button @click.stop="openModerationConfirm('issue_warning', event)" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                          Issue Warning
                        </button>
                      </div>
                    </div>
                  </div>
                  </div>

                  <div class="flex flex-wrap gap-2 mb-3">
                  <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                    {{ formatDate(event.start_date) }}
                  </span>
                  <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                    {{ formatTime(event.start_date) }} - {{ formatTime(event.end_date) }}
                  </span>
                  <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full flex items-center gap-1">
                    {{ event.location }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </span>
                  <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">
                    {{ event.type }}
                    </span>
                  </div>
                
                <p class="text-gray-700 text-sm mb-4 line-clamp-2">{{ event.description }}</p>
                
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span>{{ event.attendees_count }} attendees</span>
                    <span>{{ event.interested_count }} interested</span>
                  </div>
                  
                  <div class="flex space-x-2">
                    <button 
                      @click.stop="rsvpEvent(event, 'going')"
                      :disabled="rsvpSubmitting[event.id]"
                      :class="[
                        'px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed',
                        event.user_rsvp === 'going' 
                          ? 'bg-green-600 text-white' 
                          : 'bg-green-100 text-green-800 hover:bg-green-200'
                      ]"
                    >
                      Going
                    </button>
                    <button 
                      @click.stop="rsvpEvent(event, 'interested')"
                      :disabled="rsvpSubmitting[event.id]"
                      :class="[
                        'px-4 py-2 rounded-lg text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed',
                        event.user_rsvp === 'interested' 
                          ? 'bg-blue-600 text-white' 
                          : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                      ]"
                    >
                      Interested
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Pagination Controls -->
          <div v-if="totalPages > 1" class="mt-6 flex items-center justify-center gap-2">
            <button 
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              &lt;
            </button>
            
            <button
              v-for="page in totalPages"
              :key="page"
              @click="goToPage(page)"
              :class="[
                'px-3 py-2 border rounded-lg min-w-[40px]',
                currentPage === page 
                  ? 'bg-blue-600 text-white border-blue-600' 
                  : 'border-gray-300 hover:bg-gray-50 text-gray-700'
              ]"
            >
              {{ page }}
            </button>
            
            <button 
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              &gt;
            </button>
          </div>
        </div>
      </div>

      <!-- Right Column - Quick Panels -->
      <div class="space-y-6">
        <!-- Saved Events -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Saved Events</h3>
          <div class="space-y-3">
            <div 
              v-for="event in savedEvents" 
              :key="event.id"
              class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer"
              @click="viewEvent(event)"
            >
              <h4 class="font-medium text-gray-900 text-sm">{{ event.title }}</h4>
              <p class="text-xs text-gray-600">{{ formatDate(event.start_date) }}</p>
              <span :class="[
                'inline-block px-2 py-1 text-xs rounded-full mt-1',
                event.user_rsvp === 'going' ? 'bg-green-100 text-green-800' :
                event.user_rsvp === 'interested' ? 'bg-blue-100 text-blue-800' :
                'bg-red-100 text-red-800'
              ]">
                Status: {{ event.user_rsvp === 'going' ? 'Going' : event.user_rsvp === 'interested' ? 'Interested' : 'Not Going' }}
                    </span>
                  </div>
            <div v-if="savedEvents.length === 0" class="text-center text-gray-500 text-sm py-4">
              No saved events
            </div>
          </div>
        </div>

        <!-- Featured Events -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured Events</h3>
          <div class="space-y-3">
            <div 
              v-for="event in featuredEvents" 
              :key="event.id"
              class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer"
              @click="viewEvent(event)"
            >
              <h4 class="font-medium text-gray-900 text-sm">{{ event.title }}</h4>
              <p class="text-xs text-gray-600">{{ formatDate(event.start_date) }} • {{ event.location }}</p>
              <div class="mt-2">
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">
                  Featured
                  </span>
                  </div>
                </div>
              </div>
            </div>
      </div>
    </div>

        

    <!-- Event Detail Modal -->
    <div v-if="showEventModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showEventModal = false">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto border-2 border-black shadow-2xl">
        <div class="p-6">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-gray-900">{{ selectedEvent?.title }}</h2>
              <p class="text-gray-600">Hosted by {{ selectedEvent?.creator.full_name }}</p>
            </div>
            <button @click="showEventModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Event Details -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h3>
              <div class="space-y-3">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  <span class="text-gray-700">{{ formatDate(selectedEvent?.start_date) }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-gray-700">{{ formatTime(selectedEvent?.start_date) }} - {{ formatTime(selectedEvent?.end_date) }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <span class="text-gray-700">{{ selectedEvent?.location }}</span>
                </div>
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  <span class="text-gray-700">{{ selectedEvent?.type }}</span>
          </div>
        </div>
      </div>

            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Description</h3>
              <p class="text-gray-700">{{ selectedEvent?.description }}</p>
            </div>
          </div>

          <!-- RSVP Section -->
          <div class="border-t border-gray-200 pt-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">RSVP</h3>
            <div class="flex space-x-4">
              <button 
                @click="rsvpEvent(selectedEvent, 'going')"
                :disabled="selectedEvent && rsvpSubmitting[selectedEvent.id]"
                :class="[
                  'px-6 py-3 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed',
                  selectedEvent?.user_rsvp === 'going' 
                    ? 'bg-green-600 text-white' 
                    : 'bg-green-100 text-green-800 hover:bg-green-200'
                ]"
              >
                Going
              </button>
              <button 
                @click="rsvpEvent(selectedEvent, 'interested')"
                :disabled="selectedEvent && rsvpSubmitting[selectedEvent.id]"
                :class="[
                  'px-6 py-3 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed',
                  selectedEvent?.user_rsvp === 'interested' 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                ]"
              >
                Interested
              </button>
            </div>
          </div>

          <!-- Attendees -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendees ({{ selectedEvent?.attendees_count }})</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div 
                v-for="attendee in selectedEvent?.attendees" 
                :key="attendee.id"
                class="text-center"
              >
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm mx-auto mb-2">
                  {{ attendee.first_name[0] }}{{ attendee.last_name[0] }}
                </div>
                <p class="text-sm text-gray-900">{{ attendee.full_name }}</p>
              </div>
              </div>
                </div>

          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4 mt-6">
            <button @click="showEventModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
              Close
            </button>
            <button @click="openReportModal(selectedEvent)" class="px-6 py-2 border border-red-600 text-red-600 rounded-lg hover:bg-red-50">
              Report Event
            </button>
              </div>
            </div>
          </div>
        </div>

    <!-- Create Event Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-transparent flex items-start md:items-center justify-center z-[80] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showCreateModal = false">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl border-2 border-black shadow-2xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto" @click.stop>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Create New Event</h3>
          <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title</label>
            <input 
              v-model="newEvent.title"
              type="text"
              placeholder="Enter event title..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea 
              v-model="newEvent.description"
              rows="3"
              placeholder="Enter event description..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
              </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time</label>
              <input 
                v-model="newEvent.start_date"
                type="datetime-local"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
                </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">End Date & Time</label>
              <input 
                v-model="newEvent.end_date"
                type="datetime-local"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
                </div>
              </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
              <input 
                v-model="newEvent.location"
                type="text"
                placeholder="Enter location..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
              <select v-model="newEvent.type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="webinar">Webinar</option>
                <option value="reunion">Reunion</option>
                <option value="career-fair">Career Fair</option>
                <option value="workshop">Workshop</option>
                <option value="talk">Talk</option>
                <option value="seminar">Seminar</option>
              </select>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-2 mt-6">
          <button @click="showCreateModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="createEvent" :disabled="!newEvent.title.trim()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
            Create Event
          </button>
            </div>
          </div>
        </div>
      </div>
    <div v-if="showReportModal" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[90] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="showReportModal = false">
      <div class="bg-white rounded-lg p-6 w-full max-w-lg border-2 border-black shadow-2xl max-h-[calc(100vh-6rem)] md:max-h-[calc(100vh-8rem)] overflow-y-auto" @click.stop>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Report Event</h3>
          <button @click="showReportModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
            <select v-model="reportReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="spam">Spam</option>
              <option value="inappropriate_content">Inappropriate Content</option>
              <option value="scam_fraud">Scam or Fraud</option>
              <option value="harassment">Harassment</option>
              <option value="false_information">False Information</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea
              v-model="reportDescription"
              rows="3"
              placeholder="Provide additional details..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            ></textarea>
          </div>
        </div>
        <div class="flex justify-end space-x-2 mt-6">
          <button @click="showReportModal = false" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="submitReport" :disabled="reporting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
            Submit Report
          </button>
        </div>
      </div>
    </div>

    <div v-if="showModerationConfirm" class="fixed inset-0 bg-black/40 backdrop-blur-[1px] flex items-start md:items-center justify-center z-[100] px-4 pb-4 pt-20 md:pt-24 md:pl-64" @click.self="closeModerationConfirm">
      <div class="bg-white rounded-lg p-6 w-full max-w-md border-2 border-black shadow-2xl" @click.stop>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">{{ moderationConfirmTitle }}</h3>
          <button @click="closeModerationConfirm" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <p class="text-sm text-gray-600 mb-6">{{ moderationConfirmMessage }}</p>
        <div class="flex justify-end space-x-2">
          <button @click="closeModerationConfirm" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
            Cancel
          </button>
          <button @click="confirmModerationAction" :disabled="moderationSubmitting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
            Confirm
          </button>
        </div>
      </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from '../config/api'
import { useToast } from '../composables/useToast'

const toast = useToast()
const authStore = useAuthStore()

const formatLocalDate = (dateObj) => {
  if (!dateObj) return ''
  const year = dateObj.getFullYear()
  const month = String(dateObj.getMonth() + 1).padStart(2, '0')
  const day = String(dateObj.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const parseLocalDate = (dateString) => {
  if (!dateString) return null
  const [year, month, day] = dateString.split('-').map(Number)
  if (!year || !month || !day) return null
  return new Date(year, month - 1, day)
}

const parseEventDate = (value) => {
  if (!value) return null
  if (value instanceof Date) return value
  const stringValue = String(value)
  if (stringValue.endsWith('Z') || stringValue.includes('+')) return new Date(stringValue)
  if (stringValue.includes('T')) {
    const [datePart, timePart] = stringValue.split('T')
    return buildLocalDateTime(datePart, timePart)
  }
  if (stringValue.includes(' ')) {
    const [datePart, timePart] = stringValue.split(' ')
    return buildLocalDateTime(datePart, timePart)
  }
  return new Date(stringValue)
}

const buildLocalDateTime = (datePart, timePart = '00:00:00') => {
  const [year, month, day] = (datePart || '').split('-').map(Number)
  if (!year || !month || !day) return new Date(datePart)
  const [hour, minute, second] = (timePart || '').split(':').map(Number)
  return new Date(year, month - 1, day, hour || 0, minute || 0, second || 0)
}

const formatTaipeiDateTime = (value) => {
  if (!value) return value
  const stringValue = String(value)
  if (/[zZ]|[+-]\d{2}:\d{2}$/.test(stringValue)) return stringValue
  const [datePart, timePartRaw] = stringValue.includes('T')
    ? stringValue.split('T')
    : stringValue.split(' ')
  if (!datePart || !timePartRaw) return stringValue
  const timePart = timePartRaw.length === 5 ? `${timePartRaw}:00` : timePartRaw
  return `${datePart}T${timePart}+08:00`
}

const toUtcFromTaipei = (value) => {
  if (!value) return value
  const stringValue = String(value)
  if (/[zZ]|[+-]\d{2}:\d{2}$/.test(stringValue)) return stringValue
  const [datePart, timePartRaw] = stringValue.includes('T')
    ? stringValue.split('T')
    : stringValue.split(' ')
  if (!datePart || !timePartRaw) return stringValue
  const [year, month, day] = datePart.split('-').map(Number)
  const timePart = timePartRaw.length === 5 ? `${timePartRaw}:00` : timePartRaw
  const [hour, minute, second] = timePart.split(':').map(Number)
  if (!year || !month || !day) return stringValue
  const utcMs = Date.UTC(year, month - 1, day, (hour || 0) - 8, minute || 0, second || 0)
  return new Date(utcMs).toISOString()
}

// Reactive data
const searchQuery = ref('')
const selectedEventType = ref('')
const selectedLocation = ref('')
const selectedDateRange = ref('')
const viewMode = ref('list')
const showEventModal = ref(false)
const showCreateModal = ref(false)
const showReportModal = ref(false)
const selectedEvent = ref(null)
const currentDate = ref(new Date())
const selectedDate = ref(formatLocalDate(new Date()))
const loading = ref(false)
const reportEvent = ref(null)
const reportReason = ref('spam')
const reportDescription = ref('')
const reporting = ref(false)
const rsvpSubmitting = ref({})
const activeEventMenuId = ref(null)
const showModerationConfirm = ref(false)
const pendingModeration = ref(null)
const moderationSubmitting = ref(false)

// Pagination
const currentPage = ref(1)
const itemsPerPage = 10

const newEvent = ref({
  title: '',
  description: '',
  start_date: '',
  end_date: '',
  location: '',
  type: 'webinar'
})

// Fetch events from API
const events = ref([])
const isAdmin = computed(() => authStore.user?.role === 'admin')

const fetchEvents = async () => {
  loading.value = true
  try {
    const params = {
      search: searchQuery.value || undefined,
      upcoming: selectedDateRange.value === 'upcoming' ? true : undefined
    }
    
    const response = await axios.get('/api/events', { params })
    if (response?.data?.success && Array.isArray(response.data.data)) {
      // Parse and format event dates
      events.value = response.data.data.map(event => ({
        ...event,
        start_date: parseEventDate(event.start_date),
        end_date: parseEventDate(event.end_date),
        created_at: parseEventDate(event.created_at),
        attendees_count: event.attendees_count || 0,
        interested_count: event.interested_count || 0,
        attendees: event.attendees || [],
        interested: event.interested || [],
        user_rsvp: event.user_rsvp && event.user_rsvp !== 'not_going' ? event.user_rsvp : null,
        is_featured: !!event.is_featured
      }))
    } else {
      events.value = []
    }
  } catch (error) {
    console.error('Error fetching events:', error)
    events.value = []
  } finally {
    loading.value = false
  }
}

const createEvent = async () => {
  if (!newEvent.value.title || !newEvent.value.title.trim()) {
    toast.warning('Please enter an event title', 'Validation Error')
    return
  }

  if (!newEvent.value.description || !newEvent.value.description.trim()) {
    toast.warning('Please enter an event description', 'Validation Error')
    return
  }

  if (!newEvent.value.start_date) {
    toast.warning('Please select a start date and time', 'Validation Error')
    return
  }

  if (!newEvent.value.end_date) {
    toast.warning('Please select an end date and time', 'Validation Error')
    return
  }

  if (!newEvent.value.location || !newEvent.value.location.trim()) {
    toast.warning('Please enter a location', 'Validation Error')
    return
  }

  try {
    const payload = {
      ...newEvent.value,
      start_date: toUtcFromTaipei(newEvent.value.start_date),
      end_date: toUtcFromTaipei(newEvent.value.end_date)
    }
    const response = await axios.post('/api/events', payload)
    
    if (response.data.success) {
      // Show success message
      toast.success('Event created successfully!', 'Success')
      
      // Reset form
      newEvent.value = {
        title: '',
        description: '',
        start_date: '',
        end_date: '',
        location: '',
        type: 'webinar'
      }
      
      // Close modal
      showCreateModal.value = false
      
      // Refresh events list
      await fetchEvents()
    } else {
      toast.error('Failed to create event: ' + (response.data.message || 'Unknown error'), 'Error')
    }
  } catch (error) {
    console.error('Error creating event:', error)
    toast.error('Error creating event: ' + (error.response?.data?.message || error.message || 'Unknown error'), 'Error')
  }
}

const openReportModal = (event) => {
  if (!event) return
  reportEvent.value = event
  reportReason.value = 'spam'
  reportDescription.value = ''
  showReportModal.value = true
}

const submitReport = async () => {
  if (!reportEvent.value) return
  reporting.value = true
  try {
    const response = await axios.post('/api/reports', {
      reported_entity_type: 'event',
      reported_entity_id: reportEvent.value.id,
      reason: reportReason.value,
      description: reportDescription.value || null
    })
    if (response?.data?.success) {
      toast.success('Report submitted successfully', 'Success')
      showReportModal.value = false
    } else {
      toast.error(response.data?.message || 'Failed to submit report', 'Error')
    }
  } catch (error) {
    console.error('Error submitting report:', error)
    toast.error('Failed to submit report', 'Error')
  } finally {
    reporting.value = false
  }
}

const savedEvents = computed(() =>
  events.value.filter(event => ['going', 'interested'].includes(event.user_rsvp))
)

const featuredEvents = computed(() =>
  events.value.filter(event => event.is_featured)
)

const normalizeText = (value) => (value ?? '').toString().toLowerCase()

// Computed properties
const filteredEvents = computed(() => {
  let filtered = events.value

  // Apply search filter
  const query = searchQuery.value.trim().toLowerCase()
  if (query) {
    filtered = filtered.filter(event => 
      normalizeText(event.title).includes(query) ||
      normalizeText(event.description).includes(query) ||
      normalizeText(event.type).includes(query) ||
      normalizeText(event.location).includes(query) ||
      `${formatDate(event.start_date)} ${formatDate(event.end_date)}`.toLowerCase().includes(query)
    )
  }

  // Apply event type filter
  if (selectedEventType.value) {
    const selectedType = selectedEventType.value.toLowerCase()
    filtered = filtered.filter(event => normalizeText(event.type) === selectedType)
  }

  // Apply location filter
  if (selectedLocation.value) {
    const selectedLocationValue = selectedLocation.value.toLowerCase()
    filtered = filtered.filter(event => normalizeText(event.location).includes(selectedLocationValue))
  }

  // Apply date range filter
  if (selectedDateRange.value) {
    const now = new Date()
    filtered = filtered.filter(event => {
      switch (selectedDateRange.value) {
        case 'upcoming':
          return event.start_date > now
        case 'ongoing':
          return event.start_date <= now && event.end_date >= now
        case 'past':
          return event.end_date < now
        default:
          return true
      }
    })
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredEvents.value.length / itemsPerPage))

const paginatedEvents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredEvents.value.slice(start, end)
})

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

const currentYear = computed(() => currentDate.value.getFullYear())
const currentMonth = computed(() => {
  return currentDate.value.toLocaleDateString('en-US', { month: 'long' })
})

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())
  
  const days = []
  const today = new Date()
  
  for (let i = 0; i < 42; i++) {
    const date = new Date(startDate)
    date.setDate(startDate.getDate() + i)
    
    days.push({
      date: formatLocalDate(date),
      day: date.getDate(),
      isCurrentMonth: date.getMonth() === month,
      isToday: date.toDateString() === today.toDateString(),
      hasEvents: filteredEvents.value.some(event => 
        event.start_date.toDateString() === date.toDateString()
      ),
      isSelected: selectedDate.value === formatLocalDate(date)
    })
  }
  
  return days
})

const selectedDateEvents = computed(() => {
  if (!selectedDate.value) return []
  return filteredEvents.value.filter(event => formatLocalDate(event.start_date) === selectedDate.value)
})

const selectedDateLabel = computed(() => {
  if (!selectedDate.value) return ''
  const parsed = parseLocalDate(selectedDate.value)
  return parsed ? formatDate(parsed) : ''
})

// Methods
const getEventIcon = (type) => {
  switch (type) {
    case 'webinar': return 'WEB'
    case 'reunion': return 'REU'
    case 'career-fair': return 'JOB'
    case 'workshop': return 'WRK'
    case 'talk': return 'TALK'
    case 'seminar': return 'SEM'
    default: return 'EVT'
  }
}

const formatDate = (date) => {
  const parsed = parseEventDate(date)
  if (!parsed) return ''
  return parsed.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    timeZone: 'Asia/Taipei'
  })
}

const formatTime = (date) => {
  const parsed = parseEventDate(date)
  if (!parsed) return ''
  return parsed.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
    timeZone: 'Asia/Taipei'
  })
}

const viewEvent = (event) => {
  selectedEvent.value = event
  showEventModal.value = true
}

const rsvpEvent = async (event, rsvp) => {
  if (!event) return
  if (rsvpSubmitting.value[event.id]) return
  const requestedStatus = event.user_rsvp === rsvp ? 'not_going' : rsvp
  rsvpSubmitting.value[event.id] = true
  try {
    const response = await axios.post(`/api/events/${event.id}/rsvp`, { status: requestedStatus })
    if (response?.data?.success) {
      const returnedStatus = response.data.data?.user_rsvp ?? requestedStatus
      event.user_rsvp = returnedStatus === 'not_going' ? null : returnedStatus
      event.attendees_count = response.data.data?.attendees_count ?? event.attendees_count
      event.interested_count = response.data.data?.interested_count ?? event.interested_count
      toast.success('RSVP updated successfully', 'Success')
    } else {
      toast.error(response.data?.message || 'Failed to update RSVP', 'Error')
    }
  } catch (error) {
    if (error.response?.status === 409) {
      toast.warning('RSVP already recorded for this event', 'Notice')
      return
    }
    console.error('Error RSVP:', error)
    toast.error(error.response?.data?.message || 'Failed to update RSVP', 'Error')
  } finally {
    rsvpSubmitting.value[event.id] = false
  }
}

const toggleFeatured = async (event) => {
  if (!event || !isAdmin.value) return
  try {
    const response = await axios.post(`/api/events/${event.id}/feature`)
    if (response?.data?.success) {
      event.is_featured = !!response.data.data?.is_featured
      toast.success(event.is_featured ? 'Event featured' : 'Event unfeatured', 'Success')
    } else {
      toast.error(response.data?.message || 'Failed to update featured status', 'Error')
    }
  } catch (error) {
    console.error('Error toggling featured:', error)
    toast.error(error.response?.data?.message || 'Failed to update featured status', 'Error')
  }
}

const toggleEventMenu = (event) => {
  activeEventMenuId.value = activeEventMenuId.value === event.id ? null : event.id
}

const openModerationConfirm = (action, event) => {
  pendingModeration.value = { action, event }
  showModerationConfirm.value = true
  activeEventMenuId.value = null
}

const closeModerationConfirm = () => {
  showModerationConfirm.value = false
  pendingModeration.value = null
}

const moderationConfirmTitle = computed(() => {
  if (!pendingModeration.value) return ''
  switch (pendingModeration.value.action) {
    case 'remove_content':
      return 'Remove Content'
    case 'suspend_user':
      return 'Suspend User'
    case 'issue_warning':
      return 'Issue Warning'
    default:
      return 'Confirm Action'
  }
})

const moderationConfirmMessage = computed(() => {
  if (!pendingModeration.value) return ''
  switch (pendingModeration.value.action) {
    case 'remove_content':
      return 'This will archive the event and remove it from the feed.'
    case 'suspend_user':
      return 'This will immediately suspend the host account.'
    case 'issue_warning':
      return 'This will issue a formal warning that must be acknowledged.'
    default:
      return 'Are you sure you want to proceed?'
  }
})

const confirmModerationAction = async () => {
  if (!pendingModeration.value) return
  moderationSubmitting.value = true
  const { action, event } = pendingModeration.value
  try {
    const response = await axios.post(`/api/events/${event.id}/moderate`, { action })
    if (response?.data?.success) {
      toast.success('Action completed successfully', 'Success')
      await fetchEvents()
      closeModerationConfirm()
    } else {
      toast.error(response?.data?.message || 'Failed to complete action', 'Error')
    }
  } catch (error) {
    console.error('Error moderating event:', error)
    toast.error(error.response?.data?.message || error.message || 'Failed to complete action', 'Error')
  } finally {
    moderationSubmitting.value = false
  }
}

const selectDate = (day) => {
  selectedDate.value = day.date
}

const previousMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

onMounted(() => {
  fetchEvents()
})

// Watch for filter changes and refetch
watch([searchQuery, selectedEventType, selectedLocation, selectedDateRange], () => {
  currentPage.value = 1  // Reset to page 1 when filters change
})
</script>

<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}
</style>
