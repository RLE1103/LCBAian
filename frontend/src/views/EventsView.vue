<template>
  <div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Alumni Events</h1>
        <p class="text-gray-600 mt-1">Discover and join upcoming alumni events</p>
      </div>
      <div class="flex items-center space-x-4">
        <button 
          @click="showCreateModal = true"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
          {{ viewMode === 'list' ? 'View Calendar' : 'View List' }}
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
                day.hasEvents ? 'font-semibold' : ''
              ]"
            >
              {{ day.day }}
              <div v-if="day.hasEvents" class="w-2 h-2 bg-blue-500 rounded-full mx-auto mt-1"></div>
                </div>
              </div>
            </div>

        <!-- List View -->
        <div v-else class="space-y-4">
          <div v-if="filteredEvents.length === 0" class="text-center py-12 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p>No events found</p>
            <p class="text-sm">Try adjusting your search or filters</p>
          </div>

          <div 
            v-for="event in filteredEvents" 
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
                  <span class="text-sm text-gray-500">{{ formatTime(event.created_at) }}</span>
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
                      :class="[
                        'px-4 py-2 rounded-lg text-sm font-medium',
                        event.user_rsvp === 'going' 
                          ? 'bg-green-600 text-white' 
                          : 'bg-green-100 text-green-800 hover:bg-green-200'
                      ]"
                    >
                      Going
                    </button>
                    <button 
                      @click.stop="rsvpEvent(event, 'interested')"
                      :class="[
                        'px-4 py-2 rounded-lg text-sm font-medium',
                        event.user_rsvp === 'interested' 
                          ? 'bg-blue-600 text-white' 
                          : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                      ]"
                    >
                      Interested
                    </button>
                    <button 
                      @click.stop="rsvpEvent(event, 'not_going')"
                      :class="[
                        'px-4 py-2 rounded-lg text-sm font-medium',
                        event.user_rsvp === 'not_going' 
                          ? 'bg-red-600 text-white' 
                          : 'bg-red-100 text-red-800 hover:bg-red-200'
                      ]"
                    >
                      Not Going
                    </button>
                  </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>

          <!-- Pagination -->
        <div class="mt-6 flex justify-center">
          <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Load More Events
          </button>
                </div>
                    </div>

      <!-- Right Column - Quick Panels -->
      <div class="space-y-6">
        <!-- Your RSVP'd Events -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Your RSVP'd Events</h3>
          <div class="space-y-3">
            <div 
              v-for="event in userRsvpEvents" 
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
                {{ event.user_rsvp }}
                    </span>
                  </div>
            <div v-if="userRsvpEvents.length === 0" class="text-center text-gray-500 text-sm py-4">
              No RSVP'd events
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

        <!-- Past Events -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Past Events</h3>
          <div class="space-y-3">
            <div 
              v-for="event in pastEvents" 
              :key="event.id"
              class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer"
              @click="viewEvent(event)"
            >
              <h4 class="font-medium text-gray-900 text-sm">{{ event.title }}</h4>
              <p class="text-xs text-gray-600">{{ formatDate(event.start_date) }}</p>
              <div class="mt-2">
                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">
                  Completed
                    </span>
                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

    <!-- Event Detail Modal -->
    <div v-if="showEventModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto border-4 border-gray-300 shadow-2xl">
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
                :class="[
                  'px-6 py-3 rounded-lg font-medium',
                  selectedEvent?.user_rsvp === 'going' 
                    ? 'bg-green-600 text-white' 
                    : 'bg-green-100 text-green-800 hover:bg-green-200'
                ]"
              >
                Going
              </button>
              <button 
                @click="rsvpEvent(selectedEvent, 'interested')"
                :class="[
                  'px-6 py-3 rounded-lg font-medium',
                  selectedEvent?.user_rsvp === 'interested' 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-blue-100 text-blue-800 hover:bg-blue-200'
                ]"
              >
                Interested
              </button>
              <button 
                @click="rsvpEvent(selectedEvent, 'not_going')"
                :class="[
                  'px-6 py-3 rounded-lg font-medium',
                  selectedEvent?.user_rsvp === 'not_going' 
                    ? 'bg-red-600 text-white' 
                    : 'bg-red-100 text-red-800 hover:bg-red-200'
                ]"
              >
                Not Going
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
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Add to Calendar
            </button>
            <button class="px-6 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">
              Share Event
            </button>
              </div>
            </div>
          </div>
        </div>

    <!-- Create Event Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl border-4 border-gray-300 shadow-2xl">
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Reactive data
const searchQuery = ref('')
const selectedEventType = ref('')
const selectedLocation = ref('')
const selectedDateRange = ref('')
const viewMode = ref('list')
const showEventModal = ref(false)
const showCreateModal = ref(false)
const selectedEvent = ref(null)
const currentDate = ref(new Date())

const newEvent = ref({
  title: '',
  description: '',
  start_date: '',
  end_date: '',
  location: '',
  type: 'webinar'
})

// Sample data - replace with API calls
const events = ref([
  {
    id: 1,
    title: 'DECS General Assembly 2025',
    description: 'Annual general assembly for the Department of Computer Science alumni. Join us for updates, networking, and planning for the upcoming year.',
    start_date: new Date('2025-11-15T08:00:00'),
    end_date: new Date('2025-11-15T17:00:00'),
    location: 'LCBA MPH',
    type: 'reunion',
    creator: {
      id: 1,
      first_name: 'Yanet',
      last_name: 'Mekuriya',
      full_name: 'Yanet Mekuriya'
    },
    attendees_count: 45,
    interested_count: 23,
    user_rsvp: null,
    created_at: new Date(Date.now() - 5 * 60 * 1000),
    attendees: [
      { id: 1, first_name: 'John', last_name: 'Doe', full_name: 'John Doe' },
      { id: 2, first_name: 'Jane', last_name: 'Smith', full_name: 'Jane Smith' },
      { id: 3, first_name: 'Mike', last_name: 'Johnson', full_name: 'Mike Johnson' },
      { id: 4, first_name: 'Alice', last_name: 'Lee', full_name: 'Alice Lee' }
    ]
  },
  {
    id: 2,
    title: 'Career Development Seminar',
    description: 'Learn about career advancement strategies, networking tips, and industry trends from successful alumni.',
    start_date: new Date('2025-09-05T08:00:00'),
    end_date: new Date('2025-09-05T17:00:00'),
    location: 'LCBA MPH',
    type: 'seminar',
    creator: {
      id: 2,
      first_name: 'Lorem',
      last_name: 'Ipsum',
      full_name: 'Lorem Ipsum'
    },
    attendees_count: 32,
    interested_count: 18,
    user_rsvp: 'interested',
    created_at: new Date(Date.now() - 10 * 60 * 1000),
    attendees: [
      { id: 1, first_name: 'John', last_name: 'Doe', full_name: 'John Doe' },
      { id: 2, first_name: 'Jane', last_name: 'Smith', full_name: 'Jane Smith' }
    ]
  },
  {
    id: 3,
    title: 'Cybersecurity Awareness Seminar',
    description: 'Understanding modern cybersecurity threats and best practices for personal and professional security.',
    start_date: new Date('2025-09-25T08:00:00'),
    end_date: new Date('2025-09-25T12:00:00'),
    location: 'Zoom',
    type: 'webinar',
    creator: {
      id: 3,
      first_name: 'Lorem',
      last_name: 'Ipsum',
      full_name: 'Lorem Ipsum'
    },
    attendees_count: 28,
    interested_count: 15,
    user_rsvp: 'going',
    created_at: new Date(Date.now() - 20 * 60 * 1000),
    attendees: [
      { id: 1, first_name: 'John', last_name: 'Doe', full_name: 'John Doe' },
      { id: 2, first_name: 'Jane', last_name: 'Smith', full_name: 'Jane Smith' },
      { id: 3, first_name: 'Mike', last_name: 'Johnson', full_name: 'Mike Johnson' }
    ]
  }
])

const userRsvpEvents = ref([
  {
    id: 4,
    title: 'LCBA Job Fair',
    start_date: new Date('2025-12-15T08:00:00'),
    user_rsvp: 'going'
  }
])

const featuredEvents = ref([
  {
    id: 5,
    title: 'Calamba Job Fair',
    start_date: new Date('2025-10-15T08:00:00'),
    location: 'Calamba City Hall'
  }
])

const pastEvents = ref([
  {
    id: 6,
    title: 'Generative AI Seminar',
    start_date: new Date('2024-12-15T08:00:00')
  }
])

// Computed properties
const filteredEvents = computed(() => {
  let filtered = events.value

  // Apply search filter
  if (searchQuery.value) {
    filtered = filtered.filter(event => 
      event.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      event.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      event.type.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Apply event type filter
  if (selectedEventType.value) {
    filtered = filtered.filter(event => event.type === selectedEventType.value)
  }

  // Apply location filter
  if (selectedLocation.value) {
    filtered = filtered.filter(event => 
      event.location.toLowerCase().includes(selectedLocation.value.toLowerCase())
    )
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
      date: date.toISOString().split('T')[0],
      day: date.getDate(),
      isCurrentMonth: date.getMonth() === month,
      isToday: date.toDateString() === today.toDateString(),
      hasEvents: events.value.some(event => 
        event.start_date.toDateString() === date.toDateString()
      )
    })
  }
  
  return days
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
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

const viewEvent = (event) => {
  selectedEvent.value = event
  showEventModal.value = true
}

const rsvpEvent = (event, rsvp) => {
  if (!event) return
  
  event.user_rsvp = rsvp
  
  // Update counts
  if (rsvp === 'going') {
    event.attendees_count += 1
  } else if (rsvp === 'interested') {
    event.interested_count += 1
  }
  
  console.log(`RSVP ${rsvp} for event:`, event.title)
}

const createEvent = () => {
  if (!newEvent.value.title.trim()) return

  const event = {
    id: Date.now(),
    ...newEvent.value,
    start_date: new Date(newEvent.value.start_date),
    end_date: new Date(newEvent.value.end_date),
    creator: {
      id: 1,
      first_name: 'Current',
      last_name: 'User',
      full_name: 'Current User'
    },
    attendees_count: 0,
    interested_count: 0,
    user_rsvp: null,
    created_at: new Date(),
    attendees: []
  }

  events.value.unshift(event)
  
  // Reset form
  showCreateModal.value = false
  newEvent.value = {
    title: '',
    description: '',
    start_date: '',
    end_date: '',
    location: '',
    type: 'webinar'
  }
}

const selectDate = (day) => {
  console.log('Selected date:', day.date)
}

const previousMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

onMounted(() => {
  // Load initial data
})
</script>

<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>