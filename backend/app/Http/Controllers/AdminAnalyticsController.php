<?php

namespace App\Http\Controllers;

use App\Services\ClusteringService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminAnalyticsController extends Controller
{
    protected $clusteringService;

    public function __construct(ClusteringService $clusteringService)
    {
        $this->clusteringService = $clusteringService;
    }

    /**
     * Get cluster analytics for admin dashboard
     */
    public function getClusterReport(): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $analytics = $this->clusteringService->getClusterAnalytics();
            $profiles = $this->clusteringService->getClusterProfiles();

            return response()->json([
                'success' => true,
                'data' => [
                    'analytics' => $analytics,
                    'profiles' => $profiles
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cluster analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run clustering analysis
     */
    public function runClustering(Request $request): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            $numClusters = $request->input('clusters', 5);
            $fields = $request->input('fields', []);
            
            // Validate clusters parameter
            if ($numClusters < 2 || $numClusters > 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Number of clusters must be between 2 and 10'
                ], 400);
            }

            $result = $this->clusteringService->performClustering($numClusters, $fields);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Clustering failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed cluster information
     */
    public function getClusterDetails(Request $request): JsonResponse
    {
        try {
            $clusterId = $request->input('cluster_id');
            
            if ($clusterId === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cluster ID is required'
                ], 400);
            }

            $users = \App\Models\User::where('cluster_group', $clusterId)
                ->where('role', 'alumni')
                ->select('id', 'first_name', 'last_name', 'program', 'industry', 'skills', 'current_job_title')
                ->get();

            // Get cluster statistics
            $totalUsers = $users->count();
            $programs = $users->pluck('program')->countBy();
            $industries = $users->pluck('industry')->countBy();
            $skills = $users->pluck('skills')->flatten()->countBy()->sortDesc()->take(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'cluster_id' => $clusterId,
                    'total_users' => $totalUsers,
                    'users' => $users,
                    'statistics' => [
                        'programs' => $programs,
                        'industries' => $industries,
                        'top_skills' => $skills
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cluster details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get overall analytics dashboard data
     */
    public function getAnalyticsDashboard(): JsonResponse
    {
        if (!$this->authorizeAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        try {
            // Total Alumni
            $totalUsers = \App\Models\User::where('role', 'alumni')->count();
            
            // New Alumni (Last 30 Days)
            $newAlumniCount = \App\Models\User::where('role', 'alumni')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            // Jobs Listed (Last 30 Days)
            $jobsLast30Days = \App\Models\JobPost::where('created_at', '>=', now()->subDays(30))
                ->count();
            
            // Total Events
            $totalEvents = \App\Models\Event::count();
            
            // Get cluster analytics
            $clusterAnalytics = $this->clusteringService->getClusterAnalytics();
            
            // Get cluster insights
            $clusterInsights = $this->clusteringService->generateClusterInsights();

            return response()->json([
                'success' => true,
                'data' => [
                    'overview' => [
                        'total_alumni' => $totalUsers,
                        'new_alumni_last_30_days' => $newAlumniCount,
                        'jobs_listed_last_30_days' => $jobsLast30Days,
                        'total_events' => $totalEvents
                    ],
                    'clustering' => $clusterAnalytics,
                    'cluster_insights' => $clusterInsights
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics data',
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