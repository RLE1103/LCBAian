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
     * Calculate comprehensive similarity score with multi-factor considerations
     * Enhanced to include educational background and career preferences
     */
    public static function calculateSimilarityScore(User $user, JobPost $job): float
    {
        $userSkills = $user->skills ?? [];
        $jobRequiredSkills = $job->required_skills ?? [];
        $jobPreferredSkills = $job->preferred_skills ?? [];

        // Allow matching even without skills if education/preferences match
        $hasSkills = !empty($userSkills) && !empty($jobRequiredSkills);

        // 1. Skill Similarity (30% weight) - reduced from 40%
        $requiredSimilarity = 0.0;
        $preferredSimilarity = 0.0;
        
        if ($hasSkills) {
            $requiredSimilarity = self::calculateJaccard($userSkills, $jobRequiredSkills) * 0.25;
            
            // Calculate preferred skills similarity (part of skill weight)
            if (!empty($jobPreferredSkills)) {
                $preferredSimilarity = self::calculateJaccard($userSkills, $jobPreferredSkills) * 0.05;
            }
        }

        // 2. Education Background Matching (25% weight) - NEW
        $educationScore = self::calculateEducationScore($user, $job) * 0.25;

        // 3. Cluster-based collaborative filtering (20% weight) - reduced from 30%
        $clusterScore = self::calculateClusterScore($user, $job) * 0.2;

        // 4. Career Preferences (15% weight) - NEW
        $preferencesScore = self::calculatePreferencesScore($user, $job) * 0.15;

        // 5. Industry match (5% weight) - reduced from 15%
        $industryScore = 0.0;
        if ($user->industry && $job->industry) {
            $industryScore = (strtolower($user->industry) === strtolower($job->industry)) ? 0.05 : 0.0;
        }

        // 6. Location preference (3% weight) - reduced from 10%
        $locationScore = 0.0;
        if ($user->city && $job->location) {
            // Simple check if user's city is in job location
            $locationScore = (stripos($job->location, $user->city) !== false) ? 0.03 : 0.0;
        }

        // 7. Job recency (2% weight) - reduced from 5%
        $recencyScore = self::calculateRecencyScore($job) * 0.02;

        // Combine all scores
        // Total possible: 30% + 25% + 20% + 15% + 5% + 3% + 2% = 100%
        $totalScore = $requiredSimilarity + $preferredSimilarity + $educationScore + 
                     $clusterScore + $preferencesScore + $industryScore + 
                     $locationScore + $recencyScore;
        
        // Cap at 1.0
        return min($totalScore, 1.0);
    }

    /**
     * Calculate education background matching score
     * Matches user's educational program with job industry/field
     */
    private static function calculateEducationScore(User $user, JobPost $job): float
    {
        $score = 0.0;
        
        // Get user's education history including LCBA program
        $userPrograms = [];
        if ($user->program) {
            $userPrograms[] = $user->program;
        }
        
        // Fetch education history
        $educationHistory = \App\Models\EducationHistory::where('user_id', $user->id)->get();
        foreach ($educationHistory as $edu) {
            if (in_array($edu->level, ['college', 'masters', 'doctorate'])) {
                // Include school name as it might contain program info
                $userPrograms[] = $edu->school_name;
            }
        }
        
        // Define program-to-industry mappings
        $techPrograms = ['BSCS', 'BSCpE', 'BSIT', 'Computer Science', 'Computer Engineering', 'Information Technology'];
        $businessPrograms = ['BSA', 'BSBA', 'Business Administration', 'MBA', 'Accountancy', 'Management'];
        $educationPrograms = ['BEED', 'BSED', 'Education', 'Teaching', 'Bachelor of Elementary Education', 'Bachelor of Secondary Education'];
        $engineeringPrograms = ['Engineering', 'Civil Engineering', 'Mechanical Engineering', 'Electrical Engineering'];
        $healthPrograms = ['Nursing', 'Medicine', 'Healthcare', 'Medical', 'Health Sciences'];
        
        // Check each user program against job requirements
        foreach ($userPrograms as $program) {
            $programLower = strtolower($program);
            $industryLower = strtolower($job->industry ?? '');
            $titleLower = strtolower($job->title ?? '');
            
            // Tech programs matching
            foreach ($techPrograms as $techProg) {
                if (stripos($programLower, strtolower($techProg)) !== false) {
                    if (stripos($industryLower, 'technology') !== false || 
                        stripos($industryLower, 'software') !== false ||
                        stripos($industryLower, 'it') !== false ||
                        stripos($titleLower, 'developer') !== false ||
                        stripos($titleLower, 'engineer') !== false ||
                        stripos($titleLower, 'programmer') !== false) {
                        $score += 0.6;
                    }
                    break;
                }
            }
            
            // Business programs matching
            foreach ($businessPrograms as $bizProg) {
                if (stripos($programLower, strtolower($bizProg)) !== false) {
                    if (stripos($industryLower, 'business') !== false || 
                        stripos($industryLower, 'finance') !== false ||
                        stripos($industryLower, 'accounting') !== false ||
                        stripos($industryLower, 'management') !== false ||
                        stripos($titleLower, 'manager') !== false ||
                        stripos($titleLower, 'accountant') !== false) {
                        $score += 0.6;
                    }
                    break;
                }
            }
            
            // Education programs matching
            foreach ($educationPrograms as $eduProg) {
                if (stripos($programLower, strtolower($eduProg)) !== false) {
                    if (stripos($industryLower, 'education') !== false ||
                        stripos($industryLower, 'academic') !== false ||
                        stripos($titleLower, 'teacher') !== false ||
                        stripos($titleLower, 'instructor') !== false ||
                        stripos($titleLower, 'professor') !== false) {
                        $score += 0.6;
                    }
                    break;
                }
            }
            
            // Engineering programs matching
            foreach ($engineeringPrograms as $engProg) {
                if (stripos($programLower, strtolower($engProg)) !== false) {
                    if (stripos($industryLower, 'engineering') !== false ||
                        stripos($industryLower, 'construction') !== false ||
                        stripos($industryLower, 'manufacturing') !== false) {
                        $score += 0.6;
                    }
                    break;
                }
            }
            
            // Healthcare programs matching
            foreach ($healthPrograms as $healthProg) {
                if (stripos($programLower, strtolower($healthProg)) !== false) {
                    if (stripos($industryLower, 'health') !== false ||
                        stripos($industryLower, 'medical') !== false ||
                        stripos($industryLower, 'hospital') !== false) {
                        $score += 0.6;
                    }
                    break;
                }
            }
        }
        
        // Check for advanced degrees (Masters, PhD) - boost for senior positions
        $hasAdvancedDegree = $educationHistory->contains(function($edu) {
            return in_array($edu->level, ['masters', 'doctorate']);
        });
        
        if ($hasAdvancedDegree) {
            $experienceLevel = strtolower($job->experience_level ?? '');
            if (stripos($experienceLevel, 'senior') !== false || 
                stripos($experienceLevel, 'lead') !== false ||
                stripos($experienceLevel, 'manager') !== false) {
                $score += 0.3;
            }
        }
        
        return min($score, 1.0);
    }

    /**
     * Calculate cluster-based collaborative filtering score
     * Jobs that similar users in the same cluster have are scored higher
     */
    private static function calculateClusterScore(User $user, JobPost $job): float
    {
        // If user doesn't have a cluster assignment, return 0
        if (!$user->cluster_group) {
            return 0.0;
        }

        // Find users in the same cluster
        $clusterUsers = User::where('cluster_group', $user->cluster_group)
            ->where('id', '!=', $user->id)
            ->where('role', 'alumni')
            ->limit(20)
            ->get();

        if ($clusterUsers->isEmpty()) {
            return 0.0;
        }

        // Calculate how many users in the cluster match this job well
        $goodMatches = 0;
        foreach ($clusterUsers as $clusterUser) {
            if (empty($clusterUser->skills)) continue;
            
            $similarity = self::calculateJaccard(
                $clusterUser->skills ?? [],
                $job->required_skills ?? []
            );
            
            if ($similarity > 0.3) { // Threshold for "good match"
                $goodMatches++;
            }
        }

        // Return proportion of cluster users who match well
        return min($goodMatches / max(count($clusterUsers), 1), 1.0);
    }

    /**
     * Calculate career preferences matching score
     * Matches user's work preferences with job characteristics
     */
    private static function calculatePreferencesScore(User $user, JobPost $job): float
    {
        $score = 0.0;
        
        // Work setup preferences (remote, hybrid, on-site)
        if ($user->work_setup_preferences && is_array($user->work_setup_preferences) && $job->work_type) {
            $workTypeLower = strtolower($job->work_type);
            foreach ($user->work_setup_preferences as $pref) {
                $prefLower = strtolower($pref);
                // Handle variations: on_site -> on-site/onsite
                $prefNormalized = str_replace('_', ' ', $prefLower);
                
                if (stripos($workTypeLower, $prefLower) !== false || 
                    stripos($workTypeLower, $prefNormalized) !== false ||
                    stripos($workTypeLower, str_replace('_', '-', $prefLower)) !== false) {
                    $score += 0.4;
                    break;
                }
            }
        }
        
        // Employment type preferences (full-time, part-time, contract, internship)
        if ($user->employment_type_preferences && is_array($user->employment_type_preferences) && $job->work_type) {
            $workTypeLower = strtolower($job->work_type);
            foreach ($user->employment_type_preferences as $pref) {
                $prefLower = strtolower($pref);
                // Handle variations: full_time -> full-time/fulltime
                $prefNormalized = str_replace('_', ' ', $prefLower);
                
                if (stripos($workTypeLower, $prefLower) !== false || 
                    stripos($workTypeLower, $prefNormalized) !== false ||
                    stripos($workTypeLower, str_replace('_', '-', $prefLower)) !== false) {
                    $score += 0.3;
                    break;
                }
            }
        }
        
        // Industries of interest
        if ($user->industries_of_interest && is_array($user->industries_of_interest) && $job->industry) {
            $industryLower = strtolower($job->industry);
            foreach ($user->industries_of_interest as $interest) {
                if (stripos($industryLower, strtolower($interest)) !== false ||
                    stripos($interest, $job->industry) !== false) {
                    $score += 0.3;
                    break;
                }
            }
        }
        
        return min($score, 1.0);
    }

    /**
     * Calculate recency score for a job
     * Newer jobs get higher scores
     */
    private static function calculateRecencyScore(JobPost $job): float
    {
        $daysOld = now()->diffInDays($job->created_at);
        
        // Jobs less than 7 days old get full score
        if ($daysOld < 7) {
            return 1.0;
        }
        
        // Jobs 7-30 days old get decreasing score
        if ($daysOld < 30) {
            return 1.0 - (($daysOld - 7) / 23 * 0.5);
        }
        
        // Jobs older than 30 days get minimum score
        return 0.5;
    }

    /**
     * Get recommended jobs for a user
     */
    public function getRecommendedJobs(User $user, $limit = 10)
    {
        if (empty($user->skills)) {
            Log::info("User {$user->id} has no skills, returning recent jobs");
            return JobPost::orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        // Get all jobs
        $allJobs = JobPost::all();
        
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
        $allSkills = JobPost::pluck('required_skills')
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
