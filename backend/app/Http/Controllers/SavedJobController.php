<?php

namespace App\Http\Controllers;

use App\Models\SavedJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SavedJobController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $savedJobs = SavedJob::with('job')
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($savedJob) {
                    $job = $savedJob->job;
                    return [
                        'id' => $savedJob->id,
                        'job_id' => $savedJob->job_id,
                        'title' => $job?->title,
                        'company_name' => $job?->company_name,
                        'location' => $job?->location,
                        'work_type' => $job?->work_type,
                        'status' => $job?->status,
                        'saved_at' => $savedJob->created_at,
                    ];
                })
                ->filter(fn ($item) => !empty($item['job_id']))
                ->values();

            return response()->json([
                'success' => true,
                'data' => $savedJobs
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
                'job_id' => 'required|exists:job_posts,job_id'
            ]);

            $savedJob = SavedJob::firstOrCreate([
                'user_id' => Auth::id(),
                'job_id' => $validated['job_id']
            ]);

            $savedJob->load('job');
            $job = $savedJob->job;
            $payload = [
                'id' => $savedJob->id,
                'job_id' => $savedJob->job_id,
                'title' => $job?->title,
                'company_name' => $job?->company_name,
                'location' => $job?->location,
                'work_type' => $job?->work_type,
                'status' => $job?->status,
                'saved_at' => $savedJob->created_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $payload
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($jobId): JsonResponse
    {
        try {
            $deleted = SavedJob::where('user_id', Auth::id())
                ->where('job_id', $jobId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saved job not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Saved job removed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
