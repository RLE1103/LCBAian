<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

// Public routes
Route::post('/register', [AuthController::class , 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class , 'login'])->middleware('throttle:10,1');
Route::get('/health', [App\Http\Controllers\HealthController::class , 'check']); // Public health check
Route::get('/posts', [App\Http\Controllers\PostController::class , 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes
    Route::get('/user', [AuthController::class , 'user']);
    Route::post('/logout', [AuthController::class , 'logout']);
    Route::put('/user/profile', [AuthController::class , 'updateProfile']);
    Route::put('/user/password', [AuthController::class , 'changePassword']);
    Route::post('/accept-guidelines', [AuthController::class , 'acceptGuidelines']);
    Route::post('/user/profile-picture', [AuthController::class , 'uploadProfilePicture']);
    Route::delete('/user/profile-picture', [AuthController::class , 'deleteProfilePicture']);
    Route::get('/user/warnings/pending', [AuthController::class , 'pendingWarning']);
    Route::post('/user/warnings/{id}/acknowledge', [AuthController::class , 'acknowledgeWarning']);

        // Admin-only routes
        Route::post('/admin/create', [AuthController::class , 'createAdmin']);

        // ML Analytics routes (Admin only)
        Route::prefix('admin')->group(function () {
            Route::get('/analytics/cluster-report', [App\Http\Controllers\AdminAnalyticsController::class , 'getClusterReport']);
            Route::post('/analytics/run-clustering', [App\Http\Controllers\AdminAnalyticsController::class , 'runClustering']);
            Route::get('/analytics/cluster-details', [App\Http\Controllers\AdminAnalyticsController::class , 'getClusterDetails']);
            Route::get('/analytics/dashboard', [App\Http\Controllers\AdminAnalyticsController::class , 'getAnalyticsDashboard']);
        });

        // Job Recommendation routes
        Route::prefix('jobs')->group(function () {
            Route::get('/recommended', [App\Http\Controllers\JobRecommendationController::class , 'getRecommendedJobs']);
            Route::get('/quick-recommendations', [App\Http\Controllers\JobRecommendationController::class , 'getQuickRecommendations']);
            Route::get('/similar-users', [App\Http\Controllers\JobRecommendationController::class , 'getSimilarUsers']);
            Route::get('/skill-gap-analysis', [App\Http\Controllers\JobRecommendationController::class , 'getSkillGapAnalysis']);
            Route::get('/trending-skills', [App\Http\Controllers\JobRecommendationController::class , 'getTrendingSkills']);
            Route::post('/update-recommendations', [App\Http\Controllers\JobRecommendationController::class , 'updateRecommendations']);
            Route::get('/match-score/{jobId}', [App\Http\Controllers\JobRecommendationController::class , 'getJobMatchScore']);
        });

        // User routes
        Route::get('/users', [App\Http\Controllers\UserController::class , 'index']);
        Route::get('/users/filter-options', [App\Http\Controllers\UserController::class , 'getFilterOptions']);
        Route::get('/users/{id}', [App\Http\Controllers\UserController::class , 'show']);
        Route::put('/users/{id}', [App\Http\Controllers\UserController::class , 'update']);

        // Job routes
        Route::get('/job-posts', [App\Http\Controllers\JobPostController::class , 'index']);
        Route::get('/job-posts/filter-options', [App\Http\Controllers\JobPostController::class , 'getFilterOptions']);
        Route::get('/job-posts/{id}', [App\Http\Controllers\JobPostController::class , 'show']);
        Route::post('/job-posts', [App\Http\Controllers\JobPostController::class , 'store']);
        Route::post('/job-posts/{id}/moderate', [App\Http\Controllers\JobPostController::class , 'moderate']);

        // Saved Jobs routes
        Route::get('/saved-jobs', [App\Http\Controllers\SavedJobController::class , 'index']);
        Route::post('/saved-jobs', [App\Http\Controllers\SavedJobController::class , 'store']);
        Route::delete('/saved-jobs/{jobId}', [App\Http\Controllers\SavedJobController::class , 'destroy']);

        // Message routes
        Route::get('/messages', [App\Http\Controllers\MessageController::class , 'index']);
        Route::get('/messages/conversation/{userId}', [App\Http\Controllers\MessageController::class , 'getConversation']);
        Route::get('/messages/available-users', [App\Http\Controllers\MessageController::class , 'getAvailableUsers']);
        Route::get('/messages/poll', [App\Http\Controllers\MessageController::class , 'pollMessages']);
        Route::get('/messages/unread-count', [App\Http\Controllers\MessageController::class , 'getUnreadCount']);
        Route::post('/messages', [App\Http\Controllers\MessageController::class , 'store']);

        // Post routes (social posts)
        Route::post('/posts', [App\Http\Controllers\PostController::class , 'store']);
        Route::put('/posts/{postId}', [App\Http\Controllers\PostController::class , 'update']);
        Route::delete('/posts/{postId}', [App\Http\Controllers\PostController::class , 'destroy']);

        // Report routes
        Route::post('/reports', [App\Http\Controllers\ReportController::class , 'store']);
        Route::get('/reports', [App\Http\Controllers\ReportController::class , 'index']); // Admin only
        Route::get('/reports/{id}', [App\Http\Controllers\ReportController::class , 'show']); // Admin only
        Route::put('/reports/{id}', [App\Http\Controllers\ReportController::class , 'update']); // Admin only
        Route::post('/reports/{id}/resolve', [App\Http\Controllers\ReportController::class , 'resolve']); // Admin only
    
        // Admin Job Approval routes
        Route::get('/admin/jobs/pending', [App\Http\Controllers\AdminJobApprovalController::class , 'getPendingJobs']); // Admin only
        Route::post('/admin/jobs/{id}/approve', [App\Http\Controllers\AdminJobApprovalController::class , 'approve']); // Admin only
        Route::post('/admin/jobs/{id}/reject', [App\Http\Controllers\AdminJobApprovalController::class , 'reject']); // Admin only
        Route::get('/admin/jobs/statistics', [App\Http\Controllers\AdminJobApprovalController::class , 'getStatistics']); // Admin only
    
        // Admin User Verification routes
        Route::post('/admin/users/{id}/approve', [App\Http\Controllers\UserController::class , 'approveUser']); // Admin only
        Route::post('/admin/users/{id}/reject', [App\Http\Controllers\UserController::class , 'rejectUser']); // Admin only
        Route::post('/admin/users/{id}/employee-verify', [App\Http\Controllers\UserController::class , 'approveEmployeeVerification']); // Admin only
        Route::post('/admin/users/{id}/employee-reject', [App\Http\Controllers\UserController::class , 'rejectEmployeeVerification']); // Admin only
        Route::get('/admin/users/{id}/employee-id-image', [App\Http\Controllers\UserController::class , 'getEmployeeIdImage']); // Admin only
        Route::post('/admin/users/{id}/status', [App\Http\Controllers\UserController::class , 'updateStatus']); // Admin only
        Route::post('/admin/users/{id}/role', [App\Http\Controllers\UserController::class , 'updateRole']); // Admin only
        Route::post('/admin/users/{id}/reset-password', [App\Http\Controllers\UserController::class , 'resetPassword']); // Admin only
        Route::post('/admin/users/import-csv', [App\Http\Controllers\UserController::class , 'importCsv']); // Admin only
        Route::get('/admin-logs', [App\Http\Controllers\UserController::class , 'getAdminLogs']); // Admin only
    
        // System Health & Metrics (Admin only)
        Route::get('/admin/health/metrics', [App\Http\Controllers\HealthController::class , 'metrics']); // Admin only
    
        // Education History routes
        Route::get('/user/education', [App\Http\Controllers\EducationHistoryController::class , 'index']);
        Route::post('/user/education', [App\Http\Controllers\EducationHistoryController::class , 'store']);
        Route::put('/user/education/{id}', [App\Http\Controllers\EducationHistoryController::class , 'update']);
        Route::delete('/user/education/{id}', [App\Http\Controllers\EducationHistoryController::class , 'destroy']);

        // Birthday routes
        Route::get('/birthdays/today', [App\Http\Controllers\BirthdayController::class , 'getBirthdaysToday']);
        Route::get('/birthdays/this-week', [App\Http\Controllers\BirthdayController::class , 'getBirthdaysThisWeek']);
        Route::get('/birthdays/this-month', [App\Http\Controllers\BirthdayController::class , 'getBirthdaysThisMonth']);
        Route::get('/birthdays/upcoming', [App\Http\Controllers\BirthdayController::class , 'getUpcomingBirthdays']);
        // Event routes
        Route::get('/events', [App\Http\Controllers\EventController::class , 'index']);
        Route::get('/events/{id}', [App\Http\Controllers\EventController::class , 'show']);
        Route::post('/events', [App\Http\Controllers\EventController::class , 'store']);
        Route::post('/events/{id}/rsvp', [App\Http\Controllers\EventController::class , 'rsvp']);
        Route::post('/events/{id}/feature', [App\Http\Controllers\EventController::class , 'toggleFeatured']);
        Route::post('/events/{id}/moderate', [App\Http\Controllers\EventController::class , 'moderate']);

        // Applications
        Route::post('/applications', [App\Http\Controllers\ApplicationController::class , 'store']);

        // Skills Taxonomy routes
        Route::get('/skills', [App\Http\Controllers\SkillsTaxonomyController::class , 'index']);
        Route::get('/skills/by-category', [App\Http\Controllers\SkillsTaxonomyController::class , 'byCategory']);

        // Community routes
        Route::get('/communities', [App\Http\Controllers\CommunityController::class , 'index']);
        Route::get('/communities/{id}', [App\Http\Controllers\CommunityController::class , 'show']);
        Route::post('/communities', [App\Http\Controllers\CommunityController::class , 'store']);
        Route::post('/communities/{id}/join', [App\Http\Controllers\CommunityController::class , 'join']);
        Route::post('/communities/{id}/leave', [App\Http\Controllers\CommunityController::class , 'leave']);
});
