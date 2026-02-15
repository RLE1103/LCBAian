<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\AdminLog;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Event::with(['creator:id,first_name,last_name,email'])
                ->withCount([
                    'rsvps as attendees_count' => function ($query) {
                        $query->where('status', 'going');
                    },
                    'rsvps as interested_count' => function ($query) {
                        $query->where('status', 'interested');
                    },
                ]);
            if (Schema::hasColumn('events', 'status')) {
                $query->where('status', '!=', 'archived');
            }
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                $query->where('status', 'active');
            }

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Date range filter
            if ($request->has('upcoming') && $request->upcoming) {
                $query->where('start_date', '>=', now());
            }

            $events = $query->orderBy('start_date', 'asc')->get();
            $userId = Auth::id();
            $userRsvps = $userId ? EventRsvp::where('user_id', $userId)->pluck('status', 'event_id') : collect();

            $events = $events->map(function ($event) use ($userRsvps) {
                $status = $userRsvps->get($event->id);
                $event->user_rsvp = $status === 'not_going' ? null : $status;
                return $event;
            });

            return response()->json([
                'success' => true,
                'data' => $events
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $query = Event::with(['creator:id,first_name,last_name,email'])
                ->withCount([
                    'rsvps as attendees_count' => function ($query) {
                        $query->where('status', 'going');
                    },
                    'rsvps as interested_count' => function ($query) {
                        $query->where('status', 'interested');
                    },
                ]);
            if (Schema::hasColumn('events', 'status')) {
                $query->where('status', '!=', 'archived');
            }
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                $query->where('status', 'active');
            }
            $event = $query->findOrFail($id);
            $userId = Auth::id();
            if ($userId) {
                $status = EventRsvp::where('event_id', $event->id)
                    ->where('user_id', $userId)
                    ->value('status');
                $event->user_rsvp = $status === 'not_going' ? null : $status;
            }

            return response()->json([
                'success' => true,
                'data' => $event
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found'
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'location' => 'required|string|max:255',
                'type' => 'nullable|string|max:50',
            ]);

            $event = Event::create([
                'created_by' => Auth::id(),
                'status' => 'active',
                ...$validated
            ]);
            AdminLog::create([
                'user_id' => Auth::id(),
                'action' => 'create_event',
            ]);

            return response()->json([
                'success' => true,
                'data' => $event,
                'message' => 'Event created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function rsvp(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:going,interested,not_going',
            ]);
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
            $event = Event::findOrFail($id);

            $existing = EventRsvp::where('event_id', $event->id)
                ->where('user_id', $userId)
                ->first();

            if ($existing && $existing->status === $validated['status']) {
                return response()->json([
                    'success' => false,
                    'message' => 'RSVP already recorded',
                ], 409);
            }

            try {
                if ($existing) {
                    $existing->update(['status' => $validated['status']]);
                } else {
                    EventRsvp::create([
                        'event_id' => $event->id,
                        'user_id' => $userId,
                        'status' => $validated['status'],
                    ]);
                }
            } catch (QueryException $queryException) {
                if ((string) $queryException->getCode() === '23000') {
                    $existing = EventRsvp::where('event_id', $event->id)
                        ->where('user_id', $userId)
                        ->first();

                    if ($existing && $existing->status === $validated['status']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'RSVP already recorded',
                        ], 409);
                    }

                    if ($existing) {
                        $existing->update(['status' => $validated['status']]);
                    } else {
                        throw $queryException;
                    }
                } else {
                    throw $queryException;
                }
            }

            AdminLog::create([
                'user_id' => $userId,
                'action' => 'rsvp_event_' . $validated['status'],
            ]);

            $attendeesCount = EventRsvp::where('event_id', $event->id)
                ->where('status', 'going')
                ->count();
            $interestedCount = EventRsvp::where('event_id', $event->id)
                ->where('status', 'interested')
                ->count();

            $userRsvp = $validated['status'] === 'not_going' ? null : $validated['status'];

            return response()->json([
                'success' => true,
                'message' => 'RSVP recorded successfully',
                'data' => [
                    'attendees_count' => $attendeesCount,
                    'interested_count' => $interestedCount,
                    'user_rsvp' => $userRsvp,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleFeatured($id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            $event = Event::findOrFail($id);
            $event->is_featured = !$event->is_featured;
            $event->save();

            AdminLog::create([
                'user_id' => $user->id,
                'action' => $event->is_featured ? 'feature_event' : 'unfeature_event',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $event->id,
                    'is_featured' => $event->is_featured,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
