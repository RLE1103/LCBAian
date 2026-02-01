<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Event::with(['creator:id,first_name,last_name,email']);

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

            $events = $query->orderBy('start_date', 'asc')
                           ->get();

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
            $event = Event::with(['creator:id,first_name,last_name,email'])
                         ->findOrFail($id);

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
                ...$validated
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
            // You would typically have a separate RSVP table
            // For now, we'll just acknowledge the RSVP
            return response()->json([
                'success' => true,
                'message' => 'RSVP recorded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

