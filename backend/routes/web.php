<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrf-token', function () {
    $origin = request()->header('Origin');
    $allowedOrigins = array_filter([
        env('FRONTEND_URL'),
        'https://lcbac.vercel.app',
        'http://127.0.0.1:8080',
        'http://localhost:5173',
        'http://localhost:3000',
        'http://localhost:8080',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8080',
    ]);

    $response = response()->json(['csrf_token' => csrf_token()]);
    if ($origin && in_array($origin, $allowedOrigins, true)) {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-XSRF-TOKEN, X-Requested-With');
        $response->headers->set('Vary', 'Origin');
    }

    return $response;
});

Route::options('/csrf-token', function () {
    $origin = request()->header('Origin');
    $allowedOrigins = array_filter([
        env('FRONTEND_URL'),
        'https://lcbac.vercel.app',
        'http://127.0.0.1:8080',
        'http://localhost:5173',
        'http://localhost:3000',
        'http://localhost:8080',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8080',
    ]);

    $response = response('', 204);
    if ($origin && in_array($origin, $allowedOrigins, true)) {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-XSRF-TOKEN, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Vary', 'Origin');
    }

    return $response;
});

Route::post('/login', [AuthController::class , 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class , 'logout'])->middleware('auth');
