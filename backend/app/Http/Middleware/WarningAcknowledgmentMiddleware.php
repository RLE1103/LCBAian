<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WarningAcknowledgmentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->role === 'admin') {
            return $next($request);
        }

        $allowedPaths = [
            'api/user',
            'api/user/warnings/pending',
            'api/user/warnings/*/acknowledge',
            'api/logout'
        ];

        foreach ($allowedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        $warning = null;
        if (Schema::hasTable('user_warnings')) {
            $query = DB::table('user_warnings')->where('user_id', $user->id);
            if (Schema::hasColumn('user_warnings', 'is_acknowledged')) {
                $query->where('is_acknowledged', false);
            } elseif (Schema::hasColumn('user_warnings', 'is_read')) {
                $query->where('is_read', false);
            }
            $warning = $query->orderBy('created_at', 'desc')->first();
        }

        if ($warning) {
            return response()->json([
                'success' => false,
                'message' => 'Warning acknowledgment required.',
                'warning' => [
                    'id' => $warning->id,
                'message' => $warning->warning_message ?? $warning->message ?? null
                ]
            ], 403);
        }

        return $next($request);
    }
}
