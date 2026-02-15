<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\User;
use App\Mail\JobApprovedEmail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminJobApprovalController extends Controller
{
    /**
     * Get pending job posts for approval
     */
    public function getPendingJobs(): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $pendingJobs = JobPost::with(['poster:id,first_name,last_name,email'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $pendingJobs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pending jobs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a job post
     */
    public function approve($id): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $job = JobPost::with('poster')->findOrFail($id);
            
            $job->update(['status' => 'approved']);

            // Send notification to job poster that their job was approved
            if ($job->poster && $job->poster->email) {
                try {
                    Mail::to($job->poster->email)->send(new JobApprovedEmail($job, true));
                } catch (\Exception $e) {
                    \Log::error('Failed to send job approval email: ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Job post approved successfully',
                'data' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a job post
     */
    public function reject(Request $request, $id): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $job = JobPost::with('poster')->findOrFail($id);
            
            $job->update(['status' => 'rejected']);

            // Send notification to job poster with rejection reason
            $reason = $request->input('reason', 'Your job post did not meet our community guidelines.');
            
            if ($job->poster && $job->poster->email) {
                try {
                    Mail::to($job->poster->email)->send(new JobApprovedEmail($job, false));
                } catch (\Exception $e) {
                    \Log::error('Failed to send job rejection email: ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Job post rejected',
                'data' => $job
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job approval statistics
     */
    public function getStatistics(): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $stats = [
                'pending' => JobPost::where('status', 'pending')->count(),
                'approved' => JobPost::where('status', 'approved')->count(),
                'rejected' => JobPost::where('status', 'rejected')->count(),
                'pending_this_week' => JobPost::where('status', 'pending')
                    ->where('created_at', '>=', now()->subWeek())
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function authorizeAdmin(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }
}
