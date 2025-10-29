<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Store a new job application
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'job_id' => 'required|exists:job_posts,job_id',
                'resume_path' => 'nullable|string',
                'cover_letter' => 'nullable|string',
            ]);

            $application = \App\Models\Application::create([
                'job_id' => $validated['job_id'],
                'user_id' => Auth::id(),
                'resume_path' => $validated['resume_path'] ?? null,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $application,
                'message' => 'Application submitted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}


