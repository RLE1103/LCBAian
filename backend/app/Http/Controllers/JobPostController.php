<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class JobPostController extends Controller
{
    /**
     * Get all job posts
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = JobPost::with('poster:id,first_name,last_name,middle_name');

            // Filter by status - only show approved jobs for non-admin users
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                $query->where('status', 'approved');
            }

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            // Location filter
            if ($request->has('location') && $request->location) {
                $query->where('location', 'like', "%{$request->location}%");
            }

            // Work type filter
            if ($request->has('work_type') && $request->work_type) {
                $query->where('work_type', $request->work_type);
            }

            // Industry filter
            if ($request->has('industry') && $request->industry) {
                $query->where('industry', $request->industry);
            }

            // Experience level filter
            if ($request->has('experience_level') && $request->experience_level !== '') {
                $experienceLevel = trim($request->experience_level);
                if (preg_match('/^\d+\s*\+$/', $experienceLevel)) {
                    $minValue = (int) $experienceLevel;
                    $query->where('experience_level', '>=', $minValue);
                } elseif (preg_match('/^(\d+)\s*-\s*(\d+)$/', $experienceLevel, $matches)) {
                    $minValue = (int) $matches[1];
                    $maxValue = (int) $matches[2];
                    $query->whereBetween('experience_level', [$minValue, $maxValue]);
                } elseif (is_numeric($experienceLevel)) {
                    $query->where('experience_level', (int) $experienceLevel);
                }
            }

            $jobs = $query->orderBy('created_at', 'desc')
                         ->get();

            return response()->json([
                'success' => true,
                'data' => $jobs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific job post
     */
    public function show($id): JsonResponse
    {
        try {
            $query = JobPost::with('poster:id,first_name,last_name,middle_name');
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                $query->where('status', 'approved');
            }
            $job = $query->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }
    }

    /**
     * Create a new job post
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:200',
                'description' => 'required|string',
                'company_name' => 'nullable|string|max:150',
                'location' => 'nullable|string|max:150',
                'work_type' => 'nullable|string|max:50',
                'required_skills' => 'nullable|array',
                'preferred_skills' => 'nullable|array',
                'industry' => 'nullable|string|max:100',
                'experience_level' => 'nullable|integer|min:0|max:100',
                'salary_range' => 'nullable|string|max:50',
                'application_link' => 'nullable|url|max:500',
                'is_active' => 'boolean',
            ]);

            $job = JobPost::create([
                'posted_by_admin' => Auth::user()->role === 'admin',
                'user_id' => Auth::id(),
                'status' => 'pending',  // All new job posts are pending by default
                ...$validated
            ]);

            // Load poster relationship
            $job->load('poster:id,first_name,last_name,middle_name');

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job post submitted and pending admin approval'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unique filter values for jobs directory
     */
    public function getFilterOptions(): JsonResponse
    {
        try {
            $user = Auth::user();
            $baseQuery = JobPost::query();
            if (!$user || $user->role !== 'admin') {
                $baseQuery->where('status', 'approved');
            }

            $locations = (clone $baseQuery)->whereNotNull('location')
                ->distinct()
                ->pluck('location')
                ->filter()
                ->sort()
                ->values();

            $industries = (clone $baseQuery)->whereNotNull('industry')
                ->distinct()
                ->pluck('industry')
                ->filter()
                ->sort()
                ->values();

            $workTypes = (clone $baseQuery)->whereNotNull('work_type')
                ->distinct()
                ->pluck('work_type')
                ->filter()
                ->sort()
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'locations' => $locations,
                    'industries' => $industries,
                    'work_types' => $workTypes,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
