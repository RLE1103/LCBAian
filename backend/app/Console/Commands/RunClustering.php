<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ClusteringService;

class RunClustering extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clustering:run {--k= : Number of clusters} {--auto-k : Auto-determine optimal K}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run K-means clustering on alumni data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clusteringService = new ClusteringService();
        
        $this->info('Starting clustering process...');
        
        // Determine number of clusters
        $numClusters = 5; // Default
        
        if ($this->option('auto-k')) {
            $this->info('Finding optimal K...');
            $usersData = $clusteringService->prepareData(['program', 'industry', 'skills']);
            $vectorData = $clusteringService->buildVectors($usersData, ['program', 'industry', 'skills']);
            $numClusters = $clusteringService->findOptimalK($vectorData['vectors'], 2, 8);
            $this->info("Optimal K determined: {$numClusters}");
        } elseif ($this->option('k')) {
            $numClusters = (int) $this->option('k');
            $this->info("Using specified K: {$numClusters}");
        } else {
            $this->info("Using default K: {$numClusters}");
        }
        
        // Run clustering
        $result = $clusteringService->performClustering($numClusters, ['program', 'industry', 'skills']);
        
        if ($result['success']) {
            $this->info('✓ Clustering completed successfully!');
            $this->info("Users processed: {$result['users_processed']}");
            $this->info("Clusters created: {$result['clusters_created']}");
            
            // Display analytics
            $this->newLine();
            $this->info('Cluster Distribution:');
            foreach ($result['data']['analytics'] as $cluster) {
                $this->line("  Cluster {$cluster['cluster_id']}: {$cluster['count']} users ({$cluster['percentage']}%)");
            }
            
            // Display cluster profiles
            $this->newLine();
            $this->info('Cluster Profiles:');
            foreach ($result['data']['profiles'] as $profile) {
                $this->line("\n  Cluster {$profile['cluster_id']} ({$profile['total_users']} users):");
                $this->line("    Top Skills: " . implode(', ', array_keys(array_slice($profile['top_skills'], 0, 5))));
                $this->line("    Top Industries: " . implode(', ', array_keys($profile['top_industries'])));
                $this->line("    Top Programs: " . implode(', ', array_keys($profile['top_programs'])));
            }
        } else {
            $this->error('✗ Clustering failed: ' . $result['message']);
            return 1;
        }
        
        return 0;
    }
}
