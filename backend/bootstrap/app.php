<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Add security headers to all requests
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        
        // Add audit logging for API routes
        $middleware->appendToGroup('api', \App\Http\Middleware\AuditLogMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom exception handling for API routes
        $exceptions->renderable(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                // Log all errors
                \Log::error('API Error: ' . $e->getMessage(), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                // Helper to get user-friendly message
                $getUserFriendlyMessage = function(\Throwable $e): string {
                    if ($e instanceof \Illuminate\Validation\ValidationException) {
                        return 'Validation error. Please check your input.';
                    }
                    if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                        return 'The requested resource was not found.';
                    }
                    if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                        return 'Authentication required.';
                    }
                    if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                        return 'Route not found.';
                    }
                    return 'An error occurred. Please try again later.';
                };

                // Helper to get status code
                $getStatusCode = function(\Throwable $e): int {
                    if ($e instanceof \Illuminate\Validation\ValidationException) return 422;
                    if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) return 404;
                    if ($e instanceof \Illuminate\Auth\AuthenticationException) return 401;
                    if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                        return $e->getStatusCode();
                    }
                    return 500;
                };

                // Return user-friendly JSON response
                return response()->json([
                    'success' => false,
                    'message' => $getUserFriendlyMessage($e),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], $getStatusCode($e));
            }
        });
    })->create();
