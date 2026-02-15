<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxAttempts = 120;
        $decaySeconds = 60;
        $identifier = $request->user()?->id ? 'user:' . $request->user()->id : 'ip:' . $request->ip();
        $key = 'api:' . $identifier;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            return response()
                ->json(['message' => 'Too many requests. Please try again later.'], 429)
                ->header('Retry-After', (string) $retryAfter);
        }

        RateLimiter::hit($key, $decaySeconds);

        $response = $next($request);

        $remaining = RateLimiter::remaining($key, $maxAttempts);
        $response->headers->set('X-RateLimit-Limit', (string) $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', (string) max(0, $remaining));

        return $response;
    }
}
