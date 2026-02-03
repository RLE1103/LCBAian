<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\AccountApprovedEmail;
use App\Mail\AccountRejectedEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Get all users with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Create cache key based on query parameters
            $cacheKey = 'users_' . md5(json_encode($request->all()));
            
            // Check cache first (15 minute cache)
            $users = Cache::remember($cacheKey, 900, function () use ($request) {
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

                return $query->get();
            });

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
                'middle_name' => 'nullable|string|max:50',
                'last_name' => 'sometimes|string|max:50',
                'suffix' => 'nullable|string|max:20',
                'birthdate' => 'nullable|date',
                'headline' => 'nullable|string|max:100',
                'bio' => 'nullable|string',
                // Contact & Socials
                'linkedin_url' => 'nullable|url|max:255',
                'portfolio_url' => 'nullable|url|max:255',
                // Education
                'program' => 'nullable|string|max:255',
                'batch' => 'nullable|string|max:20',
                'highest_educational_attainment' => 'nullable|in:elementary,high_school,senior_high,bachelors,masters,doctorate',
                // Location
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                // Career
                'current_job_title' => 'nullable|string|max:100',
                'industry' => 'nullable|string|max:100',
                'experience_level' => 'nullable|string|max:50',
                'employment_status' => 'nullable|in:employed_full_time,employed_part_time,self_employed,in_study,unemployed_looking,unemployed_not_looking',
                'employment_sector' => 'nullable|in:public_government,private,ngo_nonprofit',
                'years_of_experience' => 'nullable|numeric|min:0|max:100',
                'salary_range' => 'nullable|string|max:100',
                // Skills
                'skills' => 'nullable|array',
                'skills.*' => 'string|max:100',
                // Career Preferences
                'work_setup_preferences' => 'nullable|array',
                'work_setup_preferences.*' => 'in:on_site,hybrid,remote',
                'employment_type_preferences' => 'nullable|array',
                'employment_type_preferences.*' => 'in:full_time,part_time,contract,internship',
                'industries_of_interest' => 'nullable|array',
                'industries_of_interest.*' => 'string|max:100',
                // LCBA Employee/Faculty
                'is_lcba_employee_faculty' => 'nullable|boolean',
                'lcba_employee_id' => 'nullable|string|max:50',
            ]);

            // Update skills and increment usage counts
            if (isset($validated['skills'])) {
                foreach ($validated['skills'] as $skillName) {
                    $skill = \App\Models\SkillsTaxonomy::firstOrCreate(
                        ['name' => $skillName],
                        ['category' => null]
                    );
                    $skill->incrementUsage();
                }
            }

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

    /**
     * Get unique filter values for alumni directory
     */
    public function getFilterOptions(): JsonResponse
    {
        try {
            $cities = User::where('role', 'alumni')
                ->whereNotNull('city')
                ->distinct()
                ->pluck('city')
                ->filter()
                ->sort()
                ->values();

            $industries = User::where('role', 'alumni')
                ->whereNotNull('industry')
                ->distinct()
                ->pluck('industry')
                ->filter()
                ->sort()
                ->values();

            $programs = User::where('role', 'alumni')
                ->whereNotNull('program')
                ->distinct()
                ->pluck('program')
                ->filter()
                ->sort()
                ->values();

            $batches = User::where('role', 'alumni')
                ->whereNotNull('batch')
                ->distinct()
                ->pluck('batch')
                ->filter()
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'cities' => $cities,
                    'industries' => $industries,
                    'programs' => $programs,
                    'batches' => $batches,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a user (admin only)
     */
    public function approveUser(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $user = User::findOrFail($id);

            // Prevent modifying admin accounts
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot modify admin accounts.'
                ], 403);
            }

            $user->is_verified = true;
            $user->save();

            // Clear user cache when user is updated
            Cache::forget('users_*');

            // Send approval email to user
            try {
                Mail::to($user->email)->send(new AccountApprovedEmail($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'User approved successfully.',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a user (admin only)
     */
    public function rejectUser(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (Auth::user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.'
                ], 403);
            }

            $user = User::findOrFail($id);

            // Prevent modifying admin accounts
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot modify admin accounts.'
                ], 403);
            }

            $reason = $request->input('reason', 'Your registration did not meet our verification requirements.');

            // Send rejection email to user before deleting
            try {
                Mail::to($user->email)->send(new AccountRejectedEmail($user, $reason));
            } catch (\Exception $e) {
                \Log::error('Failed to send rejection email: ' . $e->getMessage());
            }

            // Delete the user account
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User rejected and removed.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

