<?php

namespace App\Http\Controllers;

use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MentorshipController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $user = User::find($userId);

            $query = Mentorship::with([
                'mentor:id,first_name,last_name,email,headline',
                'mentee:id,first_name,last_name,email,headline'
            ]);

            // Filter by role
            if ($request->has('role')) {
                if ($request->role === 'mentor') {
                    $query->where('mentor_id', $userId);
                } elseif ($request->role === 'mentee') {
                    $query->where('mentee_id', $userId);
                }
            } else {
                // Get all mentorships where user is involved
                $query->where(function($q) use ($userId) {
                    $q->where('mentor_id', $userId)
                      ->orWhere('mentee_id', $userId);
                });
            }

            // Status filter
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $mentorships = $query->orderBy('created_at', 'desc')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $mentorships
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'mentor_id' => 'required|exists:users,id',
                'goal' => 'nullable|string|max:255',
                'preferred_slot' => 'nullable|date',
            ]);

            // Check if mentorship already exists
            $existing = Mentorship::where('mentor_id', $validated['mentor_id'])
                                 ->where('mentee_id', Auth::id())
                                 ->where('status', '!=', 'declined')
                                 ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a mentorship request with this mentor'
                ], 400);
            }

            $mentorship = Mentorship::create([
                'mentor_id' => $validated['mentor_id'],
                'mentee_id' => Auth::id(),
                'goal' => $validated['goal'] ?? null,
                'preferred_slot' => $validated['preferred_slot'] ?? null,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'data' => $mentorship,
                'message' => 'Mentorship request sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $mentorship = Mentorship::findOrFail($id);

            // Only mentor or mentee can update
            if ($mentorship->mentor_id != Auth::id() && $mentorship->mentee_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,approved,declined,completed',
                'goal' => 'nullable|string|max:255',
                'preferred_slot' => 'nullable|date',
            ]);

            $mentorship->update($validated);

            return response()->json([
                'success' => true,
                'data' => $mentorship,
                'message' => 'Mentorship updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

