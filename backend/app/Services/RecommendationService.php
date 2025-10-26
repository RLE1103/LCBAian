<?php

namespace App\Services;

use App\Models\User;
use App\Models\JobPost;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    /**
     * Calculate Jaccard similarity between two arrays
     */
    public static function calculateJaccard(array $userSkills, array $jobSkills): float
    {
        if (empty($userSkills) || empty($jobSkills)) {
            return 0.0;
        }

        // Find intersection (skills in both arrays)
        $intersection = array_intersect($userSkills, $jobSkills);
        
        // Find union (all unique skills from both arrays)
        $union = array_unique(array_merge($userSkills, $jobSkills));

        $intersectionCount = count($intersection);
        $unionCount = count($union);

        // Avoid division by zero
        if ($unionCount == 0) {
            return 0.0;
        }

        return (float) $intersectionCount / $unionCount;
    }

    /**
     * Calculate Cosine similarity between two arrays
     */
    public static function calculateCosine(array $userSkills, array $jobSkills): float
    {
        if (empty($userSkills) || empty($jobSkills)) {
            return 0.0;
        }

        // Create skill vectors
        $allSkills = array_unique(array_merge($userSkills, $jobSkills));
        
        $userVector = [];
        $jobVector = [];
        
        foreach ($allSkills as $skill) {
            $userVector[] = in_array($skill, $userSkills) ? 1 : 0;
            $jobVector[] = in_array($skill, $jobSkills) ? 1 : 0;
        }

        // Calculate dot product
        $dotProduct = 0;
        for ($i = 0; $i < count($userVector); $i++) {
            $dotProduct += $userVector[$i] * $jobVector[$i];
        }

        // Calculate magnitudes
        $userMagnitude = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $userVector)));
        $jobMagnitude = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $jobVector)));

        if ($userMagnitude == 0 || $jobMagnitude == 0) {
            return 0.0;
        }

        return $dotProduct / ($userMagnitude * $jobMagnitude);
    }

    /**
     * Calculate comprehensive similarity score
     */
    public static function calculateSimilarityScore(User $user, JobPost $job): float
    {
        $userSkills = $user->skills ?? [];
        $jobRequiredSkills = $job->required_skills ?? [];
        $jobPreferredSkills = $job->preferred_skills ?? [];

        if (empty($userSkills) || empty($jobRequiredSkills)) {
            return 0.0;
        }

        // Calculate Jaccard similarity for required skills
        $requiredSimilarity = self::calculateJaccard($userSkills, $jobRequiredSkills);
        
        // Calculate Jaccard similarity for preferred skills (weighted less)
        $preferredSimilarity = 0.0;
        if (!empty($jobPreferredSkills)) {
            $preferredSimilarity = self::calculateJaccard($userSkills, $jobPreferredSkills) * 0.3;
        }

        // Bonus for exact skill matches
        $exactMatches = count(array_intersect($userSkills, $jobRequiredSkills));
        $bonusScore = min($exactMatches * 0.1, 0.3); // Max 30% bonus

        // Combine scores
        $totalScore = $requiredSimilarity + $preferredSimilarity + $bonusScore;
        
        // Cap at 1.0
        return min($totalScore, 1.0);
    }

    /**
     * Get recommended jobs for a user
     */
    public function getRecommendedJobs(User $user, $limit = 10)
    {
        if (empty($user->skills)) {
            Log::info("User {$user->id} has no skills, returning recent jobs");
            return JobPost::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        // Get all active jobs
        $allJobs = JobPost::where('is_active', true)->get();
        
        if ($allJobs->isEmpty()) {
            return collect([]);
        }

        $scoredJobs = [];

        // Calculate similarity for each job
        foreach ($allJobs as $job) {
            $similarityScore = self::calculateSimilarityScore($user, $job);
            
            // Only include jobs with some similarity
            if ($similarityScore > 0) {
                $job->similarity_score = $similarityScore;
                $scoredJobs[] = $job;
            }
        }

        // Sort by similarity score (highest first)
        $sortedJobs = collect($scoredJobs)->sortByDesc('similarity_score');

        return $sortedJobs->take($limit)->values();
    }

    /**
     * Get job recommendations with detailed scoring
     */
    public function getDetailedRecommendations(User $user, $limit = 10)
    {
        $recommendations = $this->getRecommendedJobs($user, $limit);
        
        $detailedRecommendations = [];
        
        foreach ($recommendations as $job) {
            $userSkills = $user->skills ?? [];
            $jobRequiredSkills = $job->required_skills ?? [];
            $jobPreferredSkills = $job->preferred_skills ?? [];
            
            // Calculate match details
            $matchedSkills = array_intersect($userSkills, $jobRequiredSkills);
            $missingSkills = array_diff($jobRequiredSkills, $userSkills);
            $bonusSkills = array_intersect($userSkills, $jobPreferredSkills);
            
            $detailedRecommendations[] = [
                'job' => $job,
                'similarity_score' => $job->similarity_score,
                'match_percentage' => round($job->similarity_score * 100, 1),
                'matched_skills' => array_values($matchedSkills),
                'missing_skills' => array_values($missingSkills),
                'bonus_skills' => array_values($bonusSkills),
                'match_summary' => $this->generateMatchSummary($matchedSkills, $missingSkills, $bonusSkills)
            ];
        }
        
        return $detailedRecommendations;
    }

    /**
     * Generate human-readable match summary
     */
    private function generateMatchSummary($matchedSkills, $missingSkills, $bonusSkills)
    {
        $matchedCount = count($matchedSkills);
        $missingCount = count($missingSkills);
        $bonusCount = count($bonusSkills);
        
        if ($matchedCount > 0 && $missingCount == 0) {
            return "Perfect match! You have all required skills.";
        } elseif ($matchedCount > 0 && $missingCount <= 2) {
            return "Great match! You have {$matchedCount} required skills, missing {$missingCount}.";
        } elseif ($matchedCount > 0) {
            return "Good match! You have {$matchedCount} required skills.";
        } elseif ($bonusCount > 0) {
            return "Partial match! You have {$bonusCount} preferred skills.";
        } else {
            return "Limited match based on skills.";
        }
    }

    /**
     * Get similar users based on skills
     */
    public function getSimilarUsers(User $user, $limit = 5)
    {
        if (empty($user->skills)) {
            return collect([]);
        }

        $allUsers = User::where('id', '!=', $user->id)
            ->where('role', 'alumni')
            ->whereNotNull('skills')
            ->get();

        $similarUsers = [];

        foreach ($allUsers as $otherUser) {
            $similarity = self::calculateJaccard($user->skills, $otherUser->skills ?? []);
            
            if ($similarity > 0.2) { // Only include users with >20% similarity
                $otherUser->similarity_score = $similarity;
                $similarUsers[] = $otherUser;
            }
        }

        return collect($similarUsers)
            ->sortByDesc('similarity_score')
            ->take($limit)
            ->values();
    }

    /**
     * Update user recommendations (can be called periodically)
     */
    public function updateUserRecommendations(User $user)
    {
        $recommendations = $this->getRecommendedJobs($user, 20);
        
        // Store in cache or database for faster access
        cache()->put("user_recommendations_{$user->id}", $recommendations, 3600); // 1 hour
        
        Log::info("Updated recommendations for user {$user->id}");
        
        return $recommendations;
    }

    /**
     * Get trending skills based on job postings
     */
    public function getTrendingSkills($limit = 10)
    {
        $allSkills = JobPost::where('is_active', true)
            ->pluck('required_skills')
            ->filter()
            ->flatten()
            ->countBy()
            ->sortDesc();

        return $allSkills->take($limit)->toArray();
    }

    /**
     * Get skill gap analysis for a user
     */
    public function getSkillGapAnalysis(User $user)
    {
        if (empty($user->skills)) {
            return [
                'user_skills' => [],
                'trending_skills' => $this->getTrendingSkills(),
                'missing_trending' => [],
                'recommendations' => []
            ];
        }

        $trendingSkills = $this->getTrendingSkills();
        $userSkills = $user->skills;
        
        $missingTrending = array_diff(array_keys($trendingSkills), $userSkills);
        
        return [
            'user_skills' => $userSkills,
            'trending_skills' => $trendingSkills,
            'missing_trending' => array_values($missingTrending),
            'recommendations' => array_slice($missingTrending, 0, 5) // Top 5 missing skills
        ];
    }
}
