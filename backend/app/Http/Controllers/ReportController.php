<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Submit a new report
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'reported_entity_type' => 'required|in:job_post,user,post,comment',
                'reported_entity_id' => 'required|integer',
                'reason' => 'required|in:spam,inappropriate_content,scam_fraud,harassment,false_information,other',
                'description' => 'nullable|string|max:1000',
            ]);

            $report = Report::create([
                'reporter_user_id' => Auth::id(),
                'reported_entity_type' => $validated['reported_entity_type'],
                'reported_entity_id' => $validated['reported_entity_id'],
                'reason' => $validated['reason'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reports (Admin only)
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $status = $request->get('status', 'all');
            
            $query = Report::with(['reporter:id,first_name,last_name,email']);
            
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            $reports = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $reports
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific report (Admin only)
     */
    public function show($id): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $report = Report::with(['reporter', 'reviewer'])->findOrFail($id);
            
            // Get the reported entity details
            $reportedEntity = $report->getReportedEntity();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'report' => $report,
                    'reported_entity' => $reportedEntity
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update report status (Admin only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,under_review,resolved,dismissed',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $report = Report::findOrFail($id);
            
            $report->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'] ?? $report->admin_notes,
                'reviewed_by_admin_id' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report updated successfully',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resolve a report (Admin only)
     */
    public function resolve(Request $request, $id): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $report = Report::findOrFail($id);
            
            $report->update([
                'status' => 'resolved',
                'reviewed_by_admin_id' => Auth::id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->input('admin_notes', 'Report resolved by admin')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report resolved successfully',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve report',
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
