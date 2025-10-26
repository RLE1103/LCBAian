<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);
    
    // Admin-only routes
    Route::post('/admin/create', [AuthController::class, 'createAdmin']);
    
    // ML Analytics routes (Admin only)
    Route::prefix('admin')->group(function () {
        Route::get('/analytics/cluster-report', [App\Http\Controllers\AdminAnalyticsController::class, 'getClusterReport']);
        Route::post('/analytics/run-clustering', [App\Http\Controllers\AdminAnalyticsController::class, 'runClustering']);
        Route::get('/analytics/cluster-details', [App\Http\Controllers\AdminAnalyticsController::class, 'getClusterDetails']);
        Route::get('/analytics/dashboard', [App\Http\Controllers\AdminAnalyticsController::class, 'getAnalyticsDashboard']);
    });
    
    // Job Recommendation routes
    Route::prefix('jobs')->group(function () {
        Route::get('/recommended', [App\Http\Controllers\JobRecommendationController::class, 'getRecommendedJobs']);
        Route::get('/quick-recommendations', [App\Http\Controllers\JobRecommendationController::class, 'getQuickRecommendations']);
        Route::get('/similar-users', [App\Http\Controllers\JobRecommendationController::class, 'getSimilarUsers']);
        Route::get('/skill-gap-analysis', [App\Http\Controllers\JobRecommendationController::class, 'getSkillGapAnalysis']);
        Route::get('/trending-skills', [App\Http\Controllers\JobRecommendationController::class, 'getTrendingSkills']);
        Route::post('/update-recommendations', [App\Http\Controllers\JobRecommendationController::class, 'updateRecommendations']);
        Route::get('/match-score/{jobId}', [App\Http\Controllers\JobRecommendationController::class, 'getJobMatchScore']);
    });
    
    // Add other protected routes here as needed
    // Route::apiResource('jobs', JobController::class);
    // Route::apiResource('events', EventController::class);
    // etc.
});

