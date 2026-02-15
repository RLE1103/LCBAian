<?php

namespace App\Services;

use App\Models\User;
use Rubix\ML\Clusterers\KMeans;
use Rubix\ML\Datasets\Unlabeled;
use Rubix\ML\Transformers\OneHotEncoder;
use Rubix\ML\Transformers\NumericStringConverter;
use Illuminate\Support\Facades\Log;

class ClusteringService
{
    /**
     * Prepare alumni data for clustering
     */
    public function prepareData(array $fields = [])
    {
        // Fetch all verified users with the data needed for clustering
        $select = ['id', 'role'];
        $map = [
            'program' => 'program',
            'graduation_year' => 'batch',
            'skills' => 'skills',
            'industry' => 'industry',
            'location' => 'location',
            'city' => 'city',
            'country' => 'country',
            'experience_level' => 'experience_level',
            'current_job_title' => 'current_job_title',
            'employment_status' => 'employment_status',
            'years_of_experience' => 'years_of_experience',
            'salary_range' => 'salary_range',
        ];
        if (empty($fields)) {
            $fields = ['program','industry','experience_level','skills'];
        }
        foreach ($fields as $f) {
            if (isset($map[$f])) $select[] = $map[$f];
        }

        $usersData = User::select($select)
        ->where('role', 'alumni')
         ->whereNotNull('skills')
         ->get();

        return $usersData;
    }

    private function normalizeLocationValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return null;
        }

        $normalized = preg_replace('/\s+/', ' ', $normalized);

        if (strpos($normalized, 'bgc') !== false || strpos($normalized, 'bonifacio global city') !== false) {
            return 'Manila';
        }

        if (strpos($normalized, 'taguig') !== false) {
            return 'Manila';
        }

        if (strpos($normalized, 'metro manila') !== false) {
            return 'Manila';
        }

        if (strpos($normalized, 'manila') !== false) {
            return 'Manila';
        }

        if (strpos($normalized, 'quezon city') !== false || $normalized === 'qc') {
            return 'Quezon City';
        }

        if (strpos($normalized, 'makati') !== false) {
            return 'Makati';
        }

        return trim($value);
    }

    private function normalizeLocationDisplay(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = preg_replace('/\s+/', ' ', trim($value));
        if ($trimmed === '') {
            return null;
        }

        $normalized = strtolower($trimmed);

        if (strpos($normalized, 'bgc') !== false || strpos($normalized, 'bonifacio global city') !== false) {
            return 'Taguig City';
        }

        if (strpos($normalized, 'taguig') !== false) {
            return 'Taguig City';
        }

        if (strpos($normalized, 'quezon city') !== false || $normalized === 'qc') {
            return 'Quezon City';
        }

        if (strpos($normalized, 'makati') !== false) {
            return 'Makati City';
        }

        if (strpos($normalized, 'metro manila') !== false) {
            return 'Metro Manila';
        }

        if (strpos($normalized, 'manila') !== false) {
            return 'Manila';
        }

        return $trimmed;
    }

    /**
     * Build feature vectors for clustering
     */
    public function buildVectors($usersData, array $fields = [])
    {
        if ($usersData->isEmpty()) {
            return ['vectors' => [], 'ids' => []];
        }

        if (empty($fields)) {
            $fields = ['program','industry','experience_level','skills'];
        }

        // Collect unique categorical sets depending on selected fields
        $allPrograms = in_array('program', $fields) ? $usersData->pluck('program')->filter()->unique()->values()->toArray() : [];
        $allIndustries = in_array('industry', $fields) ? $usersData->pluck('industry')->filter()->unique()->values()->toArray() : [];
        $allExperienceLevels = in_array('experience_level', $fields) ? $usersData->pluck('experience_level')->filter()->unique()->values()->toArray() : [];
        $allLocations = in_array('location', $fields) ? $usersData->map(function ($user) {
            return $this->normalizeLocationValue($user->location);
        })->filter()->unique()->values()->toArray() : [];
        $allCities = in_array('city', $fields) ? $usersData->map(function ($user) {
            return $this->normalizeLocationValue($user->city);
        })->filter()->unique()->values()->toArray() : [];
        $allCountries = in_array('country', $fields) ? $usersData->pluck('country')->filter()->unique()->values()->toArray() : [];
        $allEmploymentStatuses = in_array('employment_status', $fields) ? $usersData->pluck('employment_status')->filter()->unique()->values()->toArray() : [];
        $allYearsOfExperience = in_array('years_of_experience', $fields) ? $usersData->pluck('years_of_experience')->filter()->unique()->values()->toArray() : [];
        $allSalaryRanges = in_array('salary_range', $fields) ? $usersData->pluck('salary_range')->filter()->unique()->values()->toArray() : [];
        $allBatches = in_array('graduation_year', $fields) ? $usersData->pluck('batch')->filter()->unique()->values()->toArray() : [];
        
        // Get all unique skills
        $allSkills = in_array('skills', $fields) ? $usersData->pluck('skills')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->toArray() : [];

        $userVectors = [];
        $userIds = [];

        foreach ($usersData as $user) {
            $vector = [];

            // Program OHE
            foreach ($allPrograms as $program) {
                $vector[] = ($user->program === $program) ? 1 : 0;
            }
            // Industry OHE
            foreach ($allIndustries as $industry) {
                $vector[] = ($user->industry === $industry) ? 1 : 0;
            }
            // Experience level OHE
            foreach ($allExperienceLevels as $level) {
                $vector[] = ($user->experience_level === $level) ? 1 : 0;
            }
            // Location OHE (legacy)
            $normalizedLocation = $this->normalizeLocationValue($user->location);
            foreach ($allLocations as $loc) {
                $vector[] = ($normalizedLocation === $loc) ? 1 : 0;
            }
            // City OHE
            $normalizedCity = $this->normalizeLocationValue($user->city);
            foreach ($allCities as $city) {
                $vector[] = ($normalizedCity === $city) ? 1 : 0;
            }
            // Country OHE
            foreach ($allCountries as $country) {
                $vector[] = ($user->country === $country) ? 1 : 0;
            }
            // Employment Status OHE
            foreach ($allEmploymentStatuses as $status) {
                $vector[] = ($user->employment_status === $status) ? 1 : 0;
            }
            // Years of Experience OHE
            foreach ($allYearsOfExperience as $yoe) {
                $vector[] = ($user->years_of_experience === $yoe) ? 1 : 0;
            }
            // Salary Range OHE
            foreach ($allSalaryRanges as $range) {
                $vector[] = ($user->salary_range === $range) ? 1 : 0;
            }
            // Graduation year/batch OHE
            foreach ($allBatches as $by) {
                $vector[] = ($user->batch === $by) ? 1 : 0;
            }
            // Skills BoW
            $userSkills = is_array($user->skills) ? $user->skills : ($user->skills ? (array)$user->skills : []);
            foreach ($allSkills as $skill) {
                $vector[] = in_array($skill, $userSkills) ? 1 : 0;
            }

            $userVectors[] = $vector;
            $userIds[] = $user->id;
        }

        return [
            'vectors' => $userVectors,
            'ids' => $userIds,
            'features' => [
                'programs' => $allPrograms,
                'industries' => $allIndustries,
                'experience_levels' => $allExperienceLevels,
                'locations' => $allLocations,
                'cities' => $allCities,
                'countries' => $allCountries,
                'employment_statuses' => $allEmploymentStatuses,
                'years_of_experience' => $allYearsOfExperience,
                'salary_ranges' => $allSalaryRanges,
                'batches' => $allBatches,
                'skills' => $allSkills
            ]
        ];
    }

    /**
     * Run K-Means clustering
     */
    public function runClustering($vectors, $ids, $numClusters = 5)
    {
        if (empty($vectors)) {
            Log::warning('No vectors provided for clustering');
            return false;
        }

        try {
            // Normalize vectors before clustering
            $normalizedVectors = $this->normalizeVectors($vectors);
            
            // Create dataset
            $dataset = new Unlabeled($normalizedVectors);

            // Initialize K-Means
            $kmeans = new KMeans($numClusters, 100, 0.001);

            // Train the clusterer
            $kmeans->train($dataset);
            
            // Predict clusters for all data points
            $predictions = $kmeans->predict($dataset);

            // Save results to database
            $this->saveClusterPredictions($predictions, $ids);

            Log::info("K-Means clustering completed with {$numClusters} clusters");
            return true;

        } catch (\Exception $e) {
            Log::error('Clustering failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Save clustering predictions to database
     */
    private function saveClusterPredictions($predictions, $ids)
    {
        foreach ($predictions as $index => $clusterId) {
            $userId = $ids[$index];
            User::where('id', $userId)->update(['cluster_group' => $clusterId]);
        }
    }

    /**
     * Normalize feature vectors using L2 normalization
     */
    private function normalizeVectors($vectors)
    {
        $normalized = [];
        foreach ($vectors as $vector) {
            // Calculate L2 norm (magnitude)
            $magnitude = sqrt(array_sum(array_map(function($v) { 
                return $v * $v; 
            }, $vector)));
            
            // Normalize if magnitude > 0
            if ($magnitude > 0) {
                $normalized[] = array_map(function($v) use ($magnitude) { 
                    return $v / $magnitude; 
                }, $vector);
            } else {
                // Keep zero vector as is
                $normalized[] = $vector;
            }
        }
        return $normalized;
    }

    /**
     * Find optimal K using Elbow Method
     * Returns the optimal number of clusters based on inertia scores
     */
    public function findOptimalK($vectors, $minK = 2, $maxK = 10)
    {
        if (empty($vectors)) {
            Log::warning('No vectors provided for optimal K selection');
            return 3; // Default fallback
        }

        $normalizedVectors = $this->normalizeVectors($vectors);
        $dataset = new Unlabeled($normalizedVectors);
        
        $inertias = [];
        
        // Calculate inertia for each K
        for ($k = $minK; $k <= $maxK; $k++) {
            try {
                $kmeans = new KMeans($k, 100, 0.001);
                
                // Train the model and get predictions
                $kmeans->train($dataset);
                $predictions = $kmeans->predict($dataset);
                
                // Get cluster assignments
                $clusters = [];
                foreach ($predictions as $index => $clusterId) {
                    if (!isset($clusters[$clusterId])) {
                        $clusters[$clusterId] = [];
                    }
                    $clusters[$clusterId][] = $index;
                }
                
                // Calculate inertia (sum of squared distances to centroids)
                $inertia = 0;
                foreach ($clusters as $clusterMembers) {
                    // Calculate cluster centroid
                    $centroid = $this->calculateCentroid($clusterMembers, $normalizedVectors);
                    
                    // Sum squared distances
                    foreach ($clusterMembers as $memberIndex) {
                        $vector = $normalizedVectors[$memberIndex];
                        $distance = $this->euclideanDistance($vector, $centroid);
                        $inertia += $distance * $distance;
                    }
                }
                
                $inertias[$k] = $inertia;
                Log::info("K={$k}, Inertia={$inertia}");
            } catch (\Exception $e) {
                Log::error("Failed to calculate inertia for K={$k}: " . $e->getMessage());
            }
        }
        
        // Find elbow point using simple rate of change method
        $optimalK = $this->findElbowPoint($inertias, $minK, $maxK);
        
        Log::info("Optimal K determined: {$optimalK}");
        return $optimalK;
    }

    /**
     * Calculate centroid of a cluster
     */
    private function calculateCentroid($memberIndices, $vectors)
    {
        if (empty($memberIndices)) {
            return [];
        }
        
        $dimensions = count($vectors[0]);
        $centroid = array_fill(0, $dimensions, 0);
        
        foreach ($memberIndices as $index) {
            foreach ($vectors[$index] as $dim => $value) {
                $centroid[$dim] += $value;
            }
        }
        
        $count = count($memberIndices);
        return array_map(function($sum) use ($count) {
            return $sum / $count;
        }, $centroid);
    }

    /**
     * Calculate Euclidean distance between two vectors
     */
    private function euclideanDistance($vector1, $vector2)
    {
        $sum = 0;
        for ($i = 0; $i < count($vector1); $i++) {
            $diff = $vector1[$i] - $vector2[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    /**
     * Find elbow point in inertia curve
     * Uses rate of change to find where improvement diminishes
     */
    private function findElbowPoint($inertias, $minK, $maxK)
    {
        if (count($inertias) < 3) {
            return $minK; // Not enough data points
        }
        
        // Calculate rate of change
        $rateOfChange = [];
        $kValues = array_keys($inertias);
        
        for ($i = 1; $i < count($kValues); $i++) {
            $k1 = $kValues[$i - 1];
            $k2 = $kValues[$i];
            $rateOfChange[$k2] = abs($inertias[$k1] - $inertias[$k2]);
        }
        
        // Find largest drop in rate of change (elbow point)
        $maxDrop = 0;
        $elbowK = $minK;
        
        for ($i = 1; $i < count($rateOfChange) - 1; $i++) {
            $k1 = $kValues[$i];
            $k2 = $kValues[$i + 1];
            
            if (isset($rateOfChange[$k1]) && isset($rateOfChange[$k2])) {
                $drop = $rateOfChange[$k1] - $rateOfChange[$k2];
                if ($drop > $maxDrop) {
                    $maxDrop = $drop;
                    $elbowK = $k1;
                }
            }
        }
        
        return $elbowK;
    }

    /**
     * Save clustering results to database
     */
    private function saveClusterResults($clusters, $ids)
    {
        foreach ($clusters as $clusterId => $cluster) {
            foreach ($cluster as $vectorIndex => $vector) {
                $userId = $ids[$vectorIndex];
                
                User::where('id', $userId)
                    ->update(['cluster_group' => $clusterId]);
            }
        }
    }

    /**
     * Get cluster analytics for admin dashboard
     */
    public function getClusterAnalytics()
    {
        $base = User::where('role', 'alumni')->whereNotNull('cluster_group');
        $total = (clone $base)->count();
        $clusters = (clone $base)
            ->select('cluster_group')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('cluster_group')
            ->orderBy('cluster_group')
            ->get();

        $analytics = [];
        foreach ($clusters as $cluster) {
            $count = $cluster->count;
            $analytics[] = [
                'cluster_id' => $cluster->cluster_group,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
            ];
        }

        return $analytics;
    }

    /**
     * Get cluster profiles (top skills, industries, etc.)
     */
    public function getClusterProfiles()
    {
        $clusterIds = User::where('role','alumni')
            ->whereNotNull('cluster_group')
            ->distinct()
            ->pluck('cluster_group');

        $profiles = [];
        foreach ($clusterIds as $cid) {
            $clusterUsers = User::where('role','alumni')->where('cluster_group', $cid)->get();
            if ($clusterUsers->isEmpty()) continue;

            $skillCounts = $clusterUsers->pluck('skills')->flatten()->countBy()->sortDesc()->take(10);
            $industryCounts = $clusterUsers->pluck('industry')->filter()->countBy()->sortDesc()->take(5);
            $programCounts = $clusterUsers->pluck('program')->filter()->countBy()->sortDesc()->take(5);
            $locationCounts = $clusterUsers->map(function ($user) {
                return $this->normalizeLocationDisplay($user->location);
            })->filter()->countBy()->sortDesc()->take(5);
            $cityCounts = $clusterUsers->map(function ($user) {
                return $this->normalizeLocationDisplay($user->city);
            })->filter()->countBy()->sortDesc()->take(5);
            $countryCounts = $clusterUsers->pluck('country')->filter()->countBy()->sortDesc()->take(5);
            $employmentStatusCounts = $clusterUsers->pluck('employment_status')->filter()->countBy()->sortDesc()->take(5);
            $yearsOfExperienceCounts = $clusterUsers->pluck('years_of_experience')->filter()->countBy()->sortDesc()->take(5);
            $salaryRangeCounts = $clusterUsers->pluck('salary_range')->filter()->countBy()->sortDesc()->take(5);
            $batchCounts = $clusterUsers->pluck('batch')->filter()->countBy()->sortDesc()->take(5);

            $profiles[] = [
                'cluster_id' => $cid,
                'total_users' => $clusterUsers->count(),
                'top_skills' => $skillCounts->toArray(),
                'top_industries' => $industryCounts->toArray(),
                'top_programs' => $programCounts->toArray(),
                'top_locations' => $locationCounts->toArray(),
                'top_cities' => $cityCounts->toArray(),
                'top_countries' => $countryCounts->toArray(),
                'top_employment_statuses' => $employmentStatusCounts->toArray(),
                'top_years_of_experience' => $yearsOfExperienceCounts->toArray(),
                'top_salary_ranges' => $salaryRangeCounts->toArray(),
                'top_batches' => $batchCounts->toArray(),
            ];
        }

        return $profiles;
    }

    /**
     * Generate human-readable insights for each cluster
     */
    public function generateClusterInsights()
    {
        $profiles = $this->getClusterProfiles();
        $insights = [];
        
        foreach ($profiles as $profile) {
            $topProgram = array_key_first($profile['top_programs'] ?? []);
            $topProgramCount = $profile['top_programs'][$topProgram] ?? 0;
            $topProgramPct = $profile['total_users'] > 0 ? round(($topProgramCount / $profile['total_users']) * 100) : 0;
            
            $topIndustry = array_key_first($profile['top_industries'] ?? []);
            $topIndustryCount = $profile['top_industries'][$topIndustry] ?? 0;
            $topIndustryPct = $profile['total_users'] > 0 ? round(($topIndustryCount / $profile['total_users']) * 100) : 0;
            
            $topCitySource = $profile['top_cities'] ?? [];
            if (empty($topCitySource)) {
                $topCitySource = $profile['top_locations'] ?? [];
            }

            $topCity = array_key_first($topCitySource);
            $topCityCount = $topCitySource[$topCity] ?? 0;
            $topCityPct = $profile['total_users'] > 0 ? round(($topCityCount / $profile['total_users']) * 100) : 0;
            
            $insight = sprintf(
                "Cluster %d represents %d alumni, with %d%% from %s program, %d%% working in %s, and %d%% located in %s.",
                $profile['cluster_id'],
                $profile['total_users'],
                $topProgramPct,
                $topProgram ?? 'various',
                $topIndustryPct,
                $topIndustry ?? 'various industries',
                $topCityPct,
                $topCity ?? 'various locations'
            );
            
            $insights[$profile['cluster_id']] = [
                'cluster_id' => $profile['cluster_id'],
                'insight' => $insight,
                'dominant_program' => $topProgram,
                'dominant_industry' => $topIndustry,
                'dominant_location' => $topCity,
                'percentages' => [
                    'program' => $topProgramPct,
                    'industry' => $topIndustryPct,
                    'location' => $topCityPct
                ],
                'total_users' => $profile['total_users']
            ];
        }
        
        return $insights;
    }

    /**
     * Run complete clustering process
     */
    public function performClustering($numClusters = 5, array $fields = [])
    {
        Log::info('Starting clustering process...');

        // 1. Prepare data
        $usersData = $this->prepareData($fields);
        Log::info('Prepared data for ' . $usersData->count() . ' users');

        // 2. Build vectors
        $vectorData = $this->buildVectors($usersData, $fields);
        $vectorCount = count($vectorData['vectors']);
        $featureCount = $vectorCount > 0 ? count($vectorData['vectors'][0]) : 0;
        Log::info('Built vectors with ' . $featureCount . ' features and ' . $vectorCount . ' records');

        if ($vectorCount === 0 || $featureCount === 0) {
            Log::warning('Clustering aborted: no usable data for selected parameters');
            return [
                'success' => false,
                'message' => 'Clustering failed: no usable data for selected parameters',
                'data' => [
                    'active_fields' => $fields
                ]
            ];
        }

        $uniqueVectorCount = collect($vectorData['vectors'])
            ->map(fn ($vector) => json_encode($vector))
            ->unique()
            ->count();

        if ($uniqueVectorCount < 2) {
            Log::warning('Clustering aborted: insufficient data variation for selected parameters');
            return [
                'success' => false,
                'message' => 'Clustering failed: insufficient data variation for selected parameters',
                'data' => [
                    'active_fields' => $fields
                ]
            ];
        }

        if ($numClusters > $uniqueVectorCount) {
            Log::warning('Requested clusters exceed unique data points, reducing cluster count');
            $numClusters = $uniqueVectorCount;
        }

        // 3. Run clustering
        $success = $this->runClustering(
            $vectorData['vectors'],
            $vectorData['ids'],
            $numClusters
        );

        if ($success) {
            $analytics = $this->getClusterAnalytics();
            $profiles = $this->getClusterProfiles();
            $insights = $this->generateClusterInsights();
            Log::info('Clustering process completed successfully');
            return [
                'success' => true,
                'message' => 'Clustering completed successfully',
                'clusters_created' => $numClusters,
                'users_processed' => count($vectorData['ids']),
                'data' => [
                    'analytics' => $analytics,
                    'profiles' => $profiles,
                    'insights' => $insights,
                    'active_fields' => $fields
                ]
            ];
        } else {
            Log::error('Clustering process failed');
            return [
                'success' => false,
                'message' => 'Clustering failed'
            ];
        }
    }
}
