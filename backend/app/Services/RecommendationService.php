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

    private static function normalizeSkillList($value): array
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->toArray();
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $value = $decoded;
            } else {
                $value = $value === '' ? [] : [$value];
            }
        }

        if (!is_array($value)) {
            $value = [];
        }

        $normalized = array_map(function ($skill) {
            if (!is_string($skill)) {
                return '';
            }
            return strtolower(trim($skill));
        }, $value);

        $normalized = array_filter($normalized, function ($skill) {
            return $skill !== '';
        });

        return array_values(array_unique($normalized));
    }

    private static function calculateOverlapRatio(array $userSkills, array $jobSkills): float
    {
        if (empty($userSkills) || empty($jobSkills)) {
            return 0.0;
        }

        $intersectionCount = count(array_intersect($userSkills, $jobSkills));
        $jobCount = count(array_unique($jobSkills));

        if ($jobCount === 0) {
            return 0.0;
        }

        return (float) $intersectionCount / $jobCount;
    }

    private static function normalizeExperienceValue($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $text = strtolower((string) $value);
        $map = [
            'entry' => 0,
            'junior' => 1,
            'mid' => 3,
            'senior' => 5,
            'lead' => 8,
            'expert' => 10,
            'manager' => 6
        ];

        foreach ($map as $key => $val) {
            if (strpos($text, $key) !== false) {
                return (float) $val;
            }
        }

        return null;
    }

    /**
     * Calculate comprehensive similarity score with multi-factor considerations
     * Enhanced to include educational background and career preferences
     */
    public static function calculateSimilarityScore(User $user, JobPost $job): float
    {
        $userSkills = self::normalizeSkillList($user->skills ?? []);
        $jobRequiredSkills = self::normalizeSkillList($job->required_skills ?? []);
        $jobPreferredSkills = self::normalizeSkillList($job->preferred_skills ?? []);

        $hasUserSkills = !empty($userSkills);
        $hasRequiredSkills = $hasUserSkills && !empty($jobRequiredSkills);

        $requiredSimilarityRaw = 0.0;
        $preferredSimilarityRaw = 0.0;

        if ($hasRequiredSkills) {
            $requiredSimilarityRaw = self::calculateJaccard($userSkills, $jobRequiredSkills);
        }

        if ($hasUserSkills && !empty($jobPreferredSkills)) {
            $preferredSimilarityRaw = self::calculateOverlapRatio($userSkills, $jobPreferredSkills);
        }

        $educationScoreRaw = self::calculateEducationScore($user, $job);

        $clusterScoreRaw = self::calculateClusterScore($user, $job);

        $preferencesScoreRaw = self::calculatePreferencesScore($user, $job);

        $careerProfileScoreRaw = self::calculateCareerProfileScore($user, $job);


        $requiredSimilarity = $requiredSimilarityRaw * 0.4;
        $preferredSimilarity = $preferredSimilarityRaw * 0.05;
        $educationScore = $educationScoreRaw * 0.2;
        $clusterScore = $clusterScoreRaw * 0.0;
        $preferencesScore = $preferencesScoreRaw * 0.2;
        $careerProfileScore = $careerProfileScoreRaw * 0.15;

        $totalScore = $requiredSimilarity + $preferredSimilarity + $educationScore +
                     $clusterScore + $preferencesScore + $careerProfileScore;

        $clusterBonus = 0.0;
        $topClusterId = self::getTopClusterForJob($job);
        if ($topClusterId !== null && $user->cluster_group !== null && (int) $user->cluster_group === (int) $topClusterId) {
            $clusterBonus = 0.1;
        }

        $totalScore += $clusterBonus;

        return min($totalScore, 1.0);
    }

    private static function getTopClusterForJob(JobPost $job): ?int
    {
        static $cachedProfiles = null;
        if ($cachedProfiles === null) {
            $service = new ClusteringService();
            $cachedProfiles = $service->getClusterProfiles();
        }
        $profiles = $cachedProfiles;
        if (empty($profiles)) {
            return null;
        }

        $jobIndustry = strtolower((string) ($job->industry ?? ''));
        $jobSkills = self::normalizeSkillList($job->required_skills ?? []);

        $bestScore = 0.0;
        $bestClusterId = null;

        foreach ($profiles as $profile) {
            $score = 0.0;
            $topIndustries = array_keys($profile['top_industries'] ?? []);
            foreach ($topIndustries as $industry) {
                $industryLower = strtolower((string) $industry);
                if ($jobIndustry !== '' && (stripos($jobIndustry, $industryLower) !== false || stripos($industryLower, $jobIndustry) !== false)) {
                    $score += 0.6;
                    break;
                }
            }

            if (!empty($jobSkills)) {
                $clusterSkills = self::normalizeSkillList(array_keys($profile['top_skills'] ?? []));
                if (!empty($clusterSkills)) {
                    $overlap = array_intersect($jobSkills, $clusterSkills);
                    $score += (count($overlap) / max(count($jobSkills), 1)) * 0.4;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestClusterId = $profile['cluster_id'] ?? null;
            }
        }

        if ($bestScore <= 0 || $bestClusterId === null) {
            return null;
        }

        return (int) $bestClusterId;
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
        if (is_string($user->program) && trim($user->program) !== '') {
            $userPrograms[] = trim($user->program);
        }
        
        $educationHistory = $user->educationHistory()->get();
        foreach ($educationHistory as $edu) {
            if (is_string($edu->school_name) && trim($edu->school_name) !== '') {
                $userPrograms[] = trim($edu->school_name);
            }
        }
        $userPrograms = array_values(array_unique($userPrograms));

        $attainmentRankMap = [
            'elementary' => 1,
            'high_school' => 2,
            'senior_high' => 3,
            'college' => 4,
            'bachelors' => 4,
            'masters' => 5,
            'doctorate' => 6,
        ];

        $attainmentLevel = $user->highest_educational_attainment;
        if (!is_string($attainmentLevel) || $attainmentLevel === '') {
            $bestRank = 0;
            $bestLevel = null;
            foreach ($educationHistory as $edu) {
                $level = $edu->level;
                $rank = $attainmentRankMap[$level] ?? 0;
                if ($rank > $bestRank) {
                    $bestRank = $rank;
                    $bestLevel = $level;
                }
            }
            $attainmentLevel = $bestLevel;
        }
        
        // Define program-to-industry mappings
        $techPrograms = ['BSCS', 'BSCpE', 'BSIT', 'Computer Science', 'Computer Engineering', 'Information Technology'];
        $businessPrograms = ['BSA', 'BSBA', 'Business Administration', 'MBA', 'Accountancy', 'Management'];
        $educationPrograms = ['BEED', 'BSED', 'Education', 'Teaching', 'Bachelor of Elementary Education', 'Bachelor of Secondary Education'];
        $engineeringPrograms = ['Engineering', 'Civil Engineering', 'Mechanical Engineering', 'Electrical Engineering'];
        $healthPrograms = ['Nursing', 'Medicine', 'Healthcare', 'Medical', 'Health Sciences'];
        
        // Check each user program against job requirements
        $industryLower = strtolower((string) ($job->industry ?? ''));
        $titleLower = strtolower((string) ($job->title ?? ''));
        foreach ($userPrograms as $program) {
            $programLower = strtolower($program);
            $programLower = preg_replace('/[^a-z0-9]+/', ' ', $programLower);
            $programLower = trim(preg_replace('/\s+/', ' ', $programLower));
            
            // Tech programs matching
            foreach ($techPrograms as $techProg) {
                $techProgLower = strtolower($techProg);
                $techProgLower = preg_replace('/[^a-z0-9]+/', ' ', $techProgLower);
                $techProgLower = trim(preg_replace('/\s+/', ' ', $techProgLower));
                if (stripos($programLower, $techProgLower) !== false) {
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
                $bizProgLower = strtolower($bizProg);
                $bizProgLower = preg_replace('/[^a-z0-9]+/', ' ', $bizProgLower);
                $bizProgLower = trim(preg_replace('/\s+/', ' ', $bizProgLower));
                if (stripos($programLower, $bizProgLower) !== false) {
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
                $eduProgLower = strtolower($eduProg);
                $eduProgLower = preg_replace('/[^a-z0-9]+/', ' ', $eduProgLower);
                $eduProgLower = trim(preg_replace('/\s+/', ' ', $eduProgLower));
                if (stripos($programLower, $eduProgLower) !== false) {
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
                $engProgLower = strtolower($engProg);
                $engProgLower = preg_replace('/[^a-z0-9]+/', ' ', $engProgLower);
                $engProgLower = trim(preg_replace('/\s+/', ' ', $engProgLower));
                if (stripos($programLower, $engProgLower) !== false) {
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
                $healthProgLower = strtolower($healthProg);
                $healthProgLower = preg_replace('/[^a-z0-9]+/', ' ', $healthProgLower);
                $healthProgLower = trim(preg_replace('/\s+/', ' ', $healthProgLower));
                if (stripos($programLower, $healthProgLower) !== false) {
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
        $hasAdvancedDegree = in_array($attainmentLevel, ['masters', 'doctorate'], true) ||
            $educationHistory->contains(function($edu) {
                return in_array($edu->level, ['masters', 'doctorate'], true);
            });
        
        if ($hasAdvancedDegree) {
            $experienceLevel = $job->experience_level ?? null;
            if (is_numeric($experienceLevel)) {
                if ((int) $experienceLevel >= 5) {
                    $score += 0.3;
                }
            } else {
                $experienceText = strtolower((string) $experienceLevel);
                if (stripos($experienceText, 'senior') !== false || 
                    stripos($experienceText, 'lead') !== false ||
                    stripos($experienceText, 'manager') !== false) {
                    $score += 0.3;
                }
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
            Log::info('recommendation_cluster_missing', [
                'user_id' => $user->id,
                'job_id' => $job->job_id ?? $job->id,
                'cluster_group' => $user->cluster_group,
            ]);
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

        if ($user->work_setup_preferences && is_array($user->work_setup_preferences) && $job->work_type) {
            $workTypeLower = strtolower($job->work_type);
            $workTypeNormalized = str_replace('_', '-', $workTypeLower);
            $workTypeCompact = str_replace(['-', ' '], '', $workTypeLower);
            foreach ($user->work_setup_preferences as $pref) {
                $prefLower = strtolower($pref);
                $prefNormalized = str_replace('_', '-', $prefLower);
                $prefCompact = str_replace(['-', ' '], '', $prefLower);
                if (in_array($workTypeCompact, ['remote', 'hybrid', 'onsite'], true)) {
                    if (stripos($workTypeLower, $prefLower) !== false || 
                        stripos($workTypeNormalized, $prefNormalized) !== false ||
                        $workTypeCompact === $prefCompact) {
                        $score += 0.4;
                        break;
                    }
                }
            }
        }

        if ($user->employment_type_preferences && is_array($user->employment_type_preferences) && $job->work_type) {
            $workTypeLower = strtolower($job->work_type);
            $workTypeNormalized = str_replace('_', '-', $workTypeLower);
            $workTypeCompact = str_replace(['-', ' '], '', $workTypeLower);
            foreach ($user->employment_type_preferences as $pref) {
                $prefLower = strtolower($pref);
                $prefNormalized = str_replace('_', '-', $prefLower);
                $prefCompact = str_replace(['-', ' '], '', $prefLower);
                if (in_array($workTypeCompact, ['fulltime', 'parttime', 'contract', 'internship'], true)) {
                    if (stripos($workTypeLower, $prefLower) !== false || 
                        stripos($workTypeNormalized, $prefNormalized) !== false ||
                        $workTypeCompact === $prefCompact) {
                        $score += 0.3;
                        break;
                    }
                }
            }
        }

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

    private static function calculateCareerProfileScore(User $user, JobPost $job): float
    {
        $score = 0.0;

        $userIndustry = strtolower(trim((string) $user->industry));
        $jobIndustry = strtolower(trim((string) $job->industry));
        if ($userIndustry !== '' && $jobIndustry !== '') {
            if ($userIndustry === $jobIndustry || stripos($jobIndustry, $userIndustry) !== false || stripos($userIndustry, $jobIndustry) !== false) {
                $score += 0.35;
            }
        }

        $userTitle = strtolower(trim((string) $user->current_job_title));
        $jobTitle = strtolower(trim((string) $job->title));
        if ($userTitle !== '' && $jobTitle !== '') {
            if (stripos($jobTitle, $userTitle) !== false || stripos($userTitle, $jobTitle) !== false) {
                $score += 0.15;
            }
        }

        $userExperience = self::normalizeExperienceValue($user->years_of_experience ?? $user->experience_level);
        $jobExperience = self::normalizeExperienceValue($job->experience_level);
        if ($userExperience !== null && $jobExperience !== null) {
            $diff = abs($userExperience - $jobExperience);
            if ($diff <= 1) {
                $score += 0.3;
            } elseif ($userExperience >= $jobExperience) {
                $score += 0.2;
            } elseif ($diff <= 3) {
                $score += 0.1;
            }
        }

        $userSalaryRange = self::parseSalaryRange($user->salary_range);
        $jobSalaryRange = self::parseSalaryRange($job->salary_range);
        if ($userSalaryRange && $jobSalaryRange) {
            [$userMin, $userMax] = $userSalaryRange;
            [$jobMin, $jobMax] = $jobSalaryRange;
            if (min($userMax, $jobMax) - max($userMin, $jobMin) >= 0) {
                $score += 0.2;
            }
        }

        return min($score, 1.0);
    }

    private static function parseSalaryRange(?string $range): ?array
    {
        if (!$range) {
            return null;
        }

        $clean = strtolower(str_replace([',', ' '], '', $range));
        preg_match_all('/\d+(?:\.\d+)?/', $clean, $matches);

        if (empty($matches[0])) {
            return null;
        }

        $numbers = array_map('floatval', $matches[0]);
        $usesK = strpos($clean, 'k') !== false;

        if ($usesK) {
            $numbers = array_map(function ($value) {
                return $value < 1000 ? $value * 1000 : $value;
            }, $numbers);
        }

        if (count($numbers) === 1) {
            return [$numbers[0], $numbers[0]];
        }

        return [min($numbers), max($numbers)];
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
            return JobPost::approved()->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }

        $poolLimit = max(((int) $limit) * 50, 200);
        $poolLimit = min($poolLimit, 500);
        $allJobs = JobPost::approved()->orderBy('created_at', 'desc')->limit($poolLimit)->get();
        
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
