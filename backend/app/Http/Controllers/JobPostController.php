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
            $query = JobPost::query();

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
            if ($request->has('experience_level') && $request->experience_level) {
                $query->where('experience_level', $request->experience_level);
            }

            // Only show active jobs by default (remove this filter since is_active doesn't exist)
            // if ($request->has('is_active')) {
            //     $query->where('is_active', $request->is_active);
            // }

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
            $job = JobPost::findOrFail($id);

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
                'experience_level' => 'nullable|string|max:50',
                'salary_range' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            $job = JobPost::create([
                'posted_by_admin' => Auth::user()->role === 'admin',
                'user_id' => Auth::id(),
                ...$validated
            ]);

            return response()->json([
                'success' => true,
                'data' => $job,
                'message' => 'Job posted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

