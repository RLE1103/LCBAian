<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class JobRecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get recommended jobs for authenticated user
     */
    public function getRecommendedJobs(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->input('limit', 10);

            // Validate limit
            if ($limit < 1 || $limit > 50) {
                $limit = 10;
            }

            $recommendations = $this->recommendationService->getDetailedRecommendations($user, $limit);

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch job recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get simple recommended jobs (for quick loading)
     */
    public function getQuickRecommendations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->input('limit', 5);

            $recommendations = $this->recommendationService->getRecommendedJobs($user, $limit);

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quick recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get similar users based on skills
     */
    public function getSimilarUsers(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $limit = $request->input('limit', 5);

            $similarUsers = $this->recommendationService->getSimilarUsers($user, $limit);

            return response()->json([
                'success' => true,
                'data' => $similarUsers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch similar users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get skill gap analysis for user
     */
    public function getSkillGapAnalysis(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $analysis = $this->recommendationService->getSkillGapAnalysis($user);

            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze skill gaps',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trending skills
     */
    public function getTrendingSkills(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            
            $trendingSkills = $this->recommendationService->getTrendingSkills($limit);

            return response()->json([
                'success' => true,
                'data' => $trendingSkills
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trending skills',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user recommendations (manual trigger)
     */
    public function updateRecommendations(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $recommendations = $this->recommendationService->updateUserRecommendations($user);

            return response()->json([
                'success' => true,
                'message' => 'Recommendations updated successfully',
                'data' => $recommendations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job match score for a specific job
     */
    public function getJobMatchScore(Request $request, $jobId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $job = \App\Models\JobPost::findOrFail($jobId);
            
            $similarityScore = RecommendationService::calculateSimilarityScore($user, $job);
            
            $userSkills = $user->skills ?? [];
            $jobRequiredSkills = $job->required_skills ?? [];
            $jobPreferredSkills = $job->preferred_skills ?? [];
            
            $matchedSkills = array_intersect($userSkills, $jobRequiredSkills);
            $missingSkills = array_diff($jobRequiredSkills, $userSkills);
            $bonusSkills = array_intersect($userSkills, $jobPreferredSkills);

            return response()->json([
                'success' => true,
                'data' => [
                    'job_id' => $jobId,
                    'similarity_score' => $similarityScore,
                    'match_percentage' => round($similarityScore * 100, 1),
                    'matched_skills' => array_values($matchedSkills),
                    'missing_skills' => array_values($missingSkills),
                    'bonus_skills' => array_values($bonusSkills),
                    'analysis' => [
                        'total_required' => count($jobRequiredSkills),
                        'user_has' => count($matchedSkills),
                        'missing_count' => count($missingSkills),
                        'bonus_count' => count($bonusSkills)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate job match score',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}