<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get all users with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::query();

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('headline', 'like', "%{$search}%");
                });
            }

            // Role filter
            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            // Program filter
            if ($request->has('program') && $request->program) {
                $query->where('program', $request->program);
            }

            // Batch filter
            if ($request->has('batch') && $request->batch) {
                $query->where('batch', $request->batch);
            }

            $users = $query->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific user by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    /**
     * Update user profile
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Only allow users to update their own profile unless they're admin
            if (Auth::id() != $user->id && Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validated = $request->validate([
                'first_name' => 'sometimes|string|max:50',
                'last_name' => 'sometimes|string|max:50',
                'headline' => 'nullable|string|max:100',
                'bio' => 'nullable|string',
                'skills' => 'nullable|array',
                'current_job_title' => 'nullable|string|max:100',
                'industry' => 'nullable|string|max:100',
                'experience_level' => 'nullable|string|max:50',
                'program' => 'nullable|string|max:50',
                'batch' => 'nullable|string|max:20',
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

