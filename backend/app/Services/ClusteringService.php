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
            'experience_level' => 'experience_level',
            'current_job_title' => 'current_job_title',
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
        $allLocations = in_array('location', $fields) ? $usersData->pluck('location')->filter()->unique()->values()->toArray() : [];
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
            // Location OHE
            foreach ($allLocations as $loc) {
                $vector[] = ($user->location === $loc) ? 1 : 0;
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
            // Create dataset
            $dataset = new Unlabeled($vectors);

            // Initialize K-Means
            $kmeans = new KMeans($numClusters, 100, 0.001);

            // Run clustering
            $clusters = $kmeans->cluster($dataset);

            // Save results to database
            $this->saveClusterResults($clusters, $ids);

            Log::info("K-Means clustering completed with {$numClusters} clusters");
            return true;

        } catch (\Exception $e) {
            Log::error('Clustering failed: ' . $e->getMessage());
            return false;
        }
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
            $locationCounts = $clusterUsers->pluck('location')->filter()->countBy()->sortDesc()->take(5);
            $batchCounts = $clusterUsers->pluck('batch')->filter()->countBy()->sortDesc()->take(5);

            $profiles[] = [
                'cluster_id' => $cid,
                'total_users' => $clusterUsers->count(),
                'top_skills' => $skillCounts->toArray(),
                'top_industries' => $industryCounts->toArray(),
                'top_programs' => $programCounts->toArray(),
                'top_locations' => $locationCounts->toArray(),
                'top_batches' => $batchCounts->toArray(),
            ];
        }

        return $profiles;
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
        Log::info('Built vectors with ' . count($vectorData['vectors']) . ' features');

        // 3. Run clustering
        $success = $this->runClustering(
            $vectorData['vectors'],
            $vectorData['ids'],
            $numClusters
        );

        if ($success) {
            $analytics = $this->getClusterAnalytics();
            $profiles = $this->getClusterProfiles();
            Log::info('Clustering process completed successfully');
            return [
                'success' => true,
                'message' => 'Clustering completed successfully',
                'clusters_created' => $numClusters,
                'users_processed' => count($vectorData['ids']),
                'data' => [
                    'analytics' => $analytics,
                    'profiles' => $profiles
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
