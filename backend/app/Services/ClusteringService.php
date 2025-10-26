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
    public function prepareData()
    {
        // Fetch all verified users with the data needed for clustering
        $usersData = User::select(
            'id',
            'program',
            'current_job_title',
            'skills',
            'industry',
            'experience_level',
            'role'
        )->where('role', 'alumni')
         ->whereNotNull('skills')
         ->get();

        return $usersData;
    }

    /**
     * Build feature vectors for clustering
     */
    public function buildVectors($usersData)
    {
        if ($usersData->isEmpty()) {
            return ['vectors' => [], 'ids' => []];
        }

        // Get all unique values for one-hot encoding
        $allPrograms = $usersData->pluck('program')->filter()->unique()->values()->toArray();
        $allIndustries = $usersData->pluck('industry')->filter()->unique()->values()->toArray();
        $allExperienceLevels = $usersData->pluck('experience_level')->filter()->unique()->values()->toArray();
        
        // Get all unique skills
        $allSkills = $usersData->pluck('skills')
            ->filter()
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        $userVectors = [];
        $userIds = [];

        foreach ($usersData as $user) {
            $vector = [];

            // 1. Vectorize Program (One-Hot Encoding)
            foreach ($allPrograms as $program) {
                $vector[] = ($user->program === $program) ? 1 : 0;
            }

            // 2. Vectorize Industry (One-Hot Encoding)
            foreach ($allIndustries as $industry) {
                $vector[] = ($user->industry === $industry) ? 1 : 0;
            }

            // 3. Vectorize Experience Level (One-Hot Encoding)
            foreach ($allExperienceLevels as $level) {
                $vector[] = ($user->experience_level === $level) ? 1 : 0;
            }

            // 4. Vectorize Skills (Bag-of-Words)
            $userSkills = $user->skills ?? [];
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
        $clusters = User::select('cluster_group')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('cluster_group')
            ->groupBy('cluster_group')
            ->orderBy('cluster_group')
            ->get();

        $analytics = [];
        foreach ($clusters as $cluster) {
            $analytics[] = [
                'cluster_id' => $cluster->cluster_group,
                'count' => $cluster->count,
                'percentage' => 0 // Will be calculated in frontend
            ];
        }

        return $analytics;
    }

    /**
     * Get cluster profiles (top skills, industries, etc.)
     */
    public function getClusterProfiles()
    {
        $profiles = [];

        for ($i = 0; $i < 5; $i++) {
            $clusterUsers = User::where('cluster_group', $i)->get();
            
            if ($clusterUsers->isEmpty()) {
                continue;
            }

            // Get top skills in this cluster
            $allSkills = $clusterUsers->pluck('skills')->flatten();
            $skillCounts = $allSkills->countBy()->sortDesc()->take(5);
            
            // Get top industries
            $industryCounts = $clusterUsers->pluck('industry')->countBy()->sortDesc()->take(3);
            
            // Get top programs
            $programCounts = $clusterUsers->pluck('program')->countBy()->sortDesc()->take(3);

            $profiles[$i] = [
                'cluster_id' => $i,
                'total_users' => $clusterUsers->count(),
                'top_skills' => $skillCounts->toArray(),
                'top_industries' => $industryCounts->toArray(),
                'top_programs' => $programCounts->toArray(),
            ];
        }

        return $profiles;
    }

    /**
     * Run complete clustering process
     */
    public function performClustering($numClusters = 5)
    {
        Log::info('Starting clustering process...');

        // 1. Prepare data
        $usersData = $this->prepareData();
        Log::info('Prepared data for ' . $usersData->count() . ' users');

        // 2. Build vectors
        $vectorData = $this->buildVectors($usersData);
        Log::info('Built vectors with ' . count($vectorData['vectors']) . ' features');

        // 3. Run clustering
        $success = $this->runClustering(
            $vectorData['vectors'],
            $vectorData['ids'],
            $numClusters
        );

        if ($success) {
            Log::info('Clustering process completed successfully');
            return [
                'success' => true,
                'message' => 'Clustering completed successfully',
                'clusters_created' => $numClusters,
                'users_processed' => count($vectorData['ids'])
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
