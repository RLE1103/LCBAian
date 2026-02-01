<?php

namespace App\Http\Controllers;

use App\Models\EducationHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EducationHistoryController extends Controller
{
    /**
     * Get all education history for the authenticated user
     */
    public function index(): JsonResponse
    {
        try {
            $educationHistory = EducationHistory::where('user_id', Auth::id())
                ->orderBy('year_graduated', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $educationHistory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch education history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a new education entry
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'level' => 'required|in:elementary,high_school,senior_high,college,masters,doctorate',
                'school_name' => 'required|string|max:200',
                'year_graduated' => 'nullable|integer|min:1950|max:' . (date('Y') + 10),
                'awards' => 'nullable|string|max:500',
                'is_lcba' => 'nullable|boolean',
            ]);

            $educationHistory = EducationHistory::create([
                'user_id' => Auth::id(),
                ...$validated
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Education entry added successfully',
                'data' => $educationHistory
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add education entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an education entry
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $educationHistory = EducationHistory::where('user_id', Auth::id())
                ->findOrFail($id);

            $validated = $request->validate([
                'level' => 'sometimes|required|in:elementary,high_school,senior_high,college,masters,doctorate',
                'school_name' => 'sometimes|required|string|max:200',
                'year_graduated' => 'nullable|integer|min:1950|max:' . (date('Y') + 10),
                'awards' => 'nullable|string|max:500',
                'is_lcba' => 'nullable|boolean',
            ]);

            $educationHistory->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Education entry updated successfully',
                'data' => $educationHistory
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update education entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an education entry
     */
    public function destroy($id): JsonResponse
    {
        try {
            $educationHistory = EducationHistory::where('user_id', Auth::id())
                ->findOrFail($id);

            $educationHistory->delete();

            return response()->json([
                'success' => true,
                'message' => 'Education entry deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete education entry',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
