<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HealthController extends Controller
{
    /**
     * Comprehensive health check endpoint
     */
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => $this->checkQueue(),
        ];

        $overallStatus = collect($checks)->every(fn($check) => $check['status'] === 'ok') ? 'healthy' : 'degraded';

        return response()->json([
            'status' => $overallStatus,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
            'system' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => config('app.env'),
            ]
        ], $overallStatus === 'healthy' ? 200 : 503);
    }

    /**
     * Check database connection
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            $userCount = DB::table('users')->count();
            
            return [
                'status' => 'ok',
                'message' => 'Database connection successful',
                'details' => [
                    'connection' => DB::connection()->getDatabaseName(),
                    'user_count' => $userCount,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Database health check failed: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database connection failed',
                'details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Check cache connection
     */
    private function checkCache(): array
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);
            
            return [
                'status' => $value === 'test' ? 'ok' : 'warning',
                'message' => $value === 'test' ? 'Cache working' : 'Cache not working properly',
                'details' => [
                    'driver' => config('cache.default'),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Cache check failed',
                'details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Check storage directory
     */
    private function checkStorage(): array
    {
        try {
            $storagePath = storage_path();
            $isWritable = is_writable($storagePath);
            
            return [
                'status' => $isWritable ? 'ok' : 'error',
                'message' => $isWritable ? 'Storage writable' : 'Storage not writable',
                'details' => [
                    'path' => $storagePath,
                    'writable' => $isWritable,
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage check failed',
                'details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Check queue status
     */
    private function checkQueue(): array
    {
        try {
            $failedJobsCount = DB::table('failed_jobs')->count();
            
            return [
                'status' => $failedJobsCount > 10 ? 'warning' : 'ok',
                'message' => $failedJobsCount > 10 ? 'High number of failed jobs' : 'Queue healthy',
                'details' => [
                    'failed_jobs' => $failedJobsCount,
                    'driver' => config('queue.default'),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Queue check failed',
                'details' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Get system metrics (admin only)
     */
    public function metrics(): JsonResponse
    {
        try {
            $metrics = [
                'users' => [
                    'total' => DB::table('users')->count(),
                    'verified' => DB::table('users')->where('is_verified', true)->count(),
                    'pending' => DB::table('users')->where('is_verified', false)->count(),
                    'alumni' => DB::table('users')->where('role', 'alumni')->count(),
                    'admins' => DB::table('users')->where('role', 'admin')->count(),
                ],
                'jobs' => [
                    'total' => DB::table('job_posts')->count(),
                    'active' => DB::table('job_posts')->where('status', 'approved')->count(),
                    'pending' => DB::table('job_posts')->where('status', 'pending')->count(),
                ],
                'events' => [
                    'total' => DB::table('events')->count(),
                    'upcoming' => DB::table('events')->where('start_date', '>', now())->count(),
                ],
                'messages' => [
                    'total' => DB::table('messages')->count(),
                    'today' => DB::table('messages')->whereDate('created_at', today())->count(),
                ],
                'reports' => [
                    'total' => DB::table('reports')->count(),
                    'pending' => DB::table('reports')->where('status', 'pending')->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
