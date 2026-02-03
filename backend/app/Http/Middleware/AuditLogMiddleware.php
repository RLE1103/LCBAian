<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AdminLog;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request and log admin actions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated admin users
        if (Auth::check() && Auth::user()->role === 'admin') {
            $this->logAdminAction($request, $response);
        }

        return $response;
    }

    /**
     * Log admin action to database
     */
    private function logAdminAction(Request $request, Response $response)
    {
        try {
            // Only log state-changing operations (POST, PUT, DELETE, PATCH)
            if (!in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                return;
            }

            // Determine action type from route
            $action = $this->determineAction($request);

            // Skip logging for non-admin routes or health checks
            if (!$action || str_contains($request->path(), 'health')) {
                return;
            }

            AdminLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'model_type' => $this->getModelType($request),
                'model_id' => $this->getModelId($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'details' => json_encode([
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'status_code' => $response->getStatusCode(),
                    'request_data' => $this->filterSensitiveData($request->except(['password', 'password_confirmation', 'token'])),
                ]),
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the request
            Log::error('Audit logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine action from request
     */
    private function determineAction(Request $request): ?string
    {
        $path = $request->path();
        $method = $request->method();

        // Admin routes
        if (str_contains($path, 'admin/users') && str_contains($path, 'approve')) return 'approve_user';
        if (str_contains($path, 'admin/users') && str_contains($path, 'reject')) return 'reject_user';
        if (str_contains($path, 'admin/jobs') && str_contains($path, 'approve')) return 'approve_job';
        if (str_contains($path, 'admin/jobs') && str_contains($path, 'reject')) return 'reject_job';
        if (str_contains($path, 'admin/jobs') && str_contains($path, 'flag')) return 'flag_job';
        if (str_contains($path, 'reports') && str_contains($path, 'resolve')) return 'resolve_report';
        if (str_contains($path, 'admin/analytics/run-clustering')) return 'run_clustering';

        // Generic CRUD operations
        if ($method === 'POST' && !str_contains($path, 'login')) return 'create';
        if ($method === 'PUT' || $method === 'PATCH') return 'update';
        if ($method === 'DELETE') return 'delete';

        return null;
    }

    /**
     * Get model type from request
     */
    private function getModelType(Request $request): ?string
    {
        $path = $request->path();

        if (str_contains($path, 'users')) return 'User';
        if (str_contains($path, 'jobs') || str_contains($path, 'job-posts')) return 'JobPost';
        if (str_contains($path, 'reports')) return 'Report';
        if (str_contains($path, 'events')) return 'Event';
        if (str_contains($path, 'posts')) return 'Post';

        return null;
    }

    /**
     * Get model ID from request
     */
    private function getModelId(Request $request): ?int
    {
        // Extract ID from path (e.g., /api/users/123)
        preg_match('/\/(\d+)/', $request->path(), $matches);
        return isset($matches[1]) ? (int)$matches[1] : null;
    }

    /**
     * Filter sensitive data from request
     */
    private function filterSensitiveData(array $data): array
    {
        $sensitiveKeys = ['password', 'token', 'api_key', 'secret', 'credit_card'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }
}
