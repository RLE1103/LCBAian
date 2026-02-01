<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BirthdayController extends Controller
{
    /**
     * Get alumni celebrating birthdays today
     */
    public function getBirthdaysToday(): JsonResponse
    {
        try {
            $today = now();
            
            $birthdays = User::where('role', 'alumni')
                ->whereNotNull('birthdate')
                ->whereRaw('MONTH(birthdate) = ?', [$today->month])
                ->whereRaw('DAY(birthdate) = ?', [$today->day])
                ->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'profile_picture', 'headline', 'birthdate', 'privacy_settings')
                ->get();

            // Filter based on privacy settings
            $visibleBirthdays = $birthdays->filter(function ($user) {
                return $user->canViewField('birthdate', Auth::user());
            })->values();

            return response()->json([
                'success' => true,
                'data' => $visibleBirthdays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch birthdays',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get alumni celebrating birthdays this week
     */
    public function getBirthdaysThisWeek(): JsonResponse
    {
        try {
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            // Get all alumni with birthdays
            $birthdays = User::where('role', 'alumni')
                ->whereNotNull('birthdate')
                ->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'profile_picture', 'headline', 'birthdate', 'privacy_settings')
                ->get();

            // Filter by week (considering month/day only, not year)
            $weekBirthdays = $birthdays->filter(function ($user) use ($startOfWeek, $endOfWeek) {
                $birthdate = $user->birthdate;
                $birthdayThisYear = now()->setMonth($birthdate->month)->setDay($birthdate->day);
                
                return $birthdayThisYear->between($startOfWeek, $endOfWeek);
            });

            // Filter based on privacy settings
            $visibleBirthdays = $weekBirthdays->filter(function ($user) {
                return $user->canViewField('birthdate', Auth::user());
            })->values();

            // Sort by date (month and day)
            $sorted = $visibleBirthdays->sortBy(function ($user) {
                return $user->birthdate->format('m-d');
            })->values();

            return response()->json([
                'success' => true,
                'data' => $sorted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch birthdays this week',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get alumni celebrating birthdays this month
     */
    public function getBirthdaysThisMonth(): JsonResponse
    {
        try {
            $currentMonth = now()->month;

            $birthdays = User::where('role', 'alumni')
                ->whereNotNull('birthdate')
                ->whereRaw('MONTH(birthdate) = ?', [$currentMonth])
                ->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'profile_picture', 'headline', 'birthdate', 'privacy_settings')
                ->orderByRaw('DAY(birthdate) ASC')
                ->get();

            // Filter based on privacy settings
            $visibleBirthdays = $birthdays->filter(function ($user) {
                return $user->canViewField('birthdate', Auth::user());
            })->values();

            return response()->json([
                'success' => true,
                'data' => $visibleBirthdays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch birthdays this month',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming birthdays (next 30 days)
     */
    public function getUpcomingBirthdays(): JsonResponse
    {
        try {
            $today = now();
            $next30Days = now()->addDays(30);

            $birthdays = User::where('role', 'alumni')
                ->whereNotNull('birthdate')
                ->select('id', 'first_name', 'middle_name', 'last_name', 'suffix', 'profile_picture', 'headline', 'birthdate', 'privacy_settings')
                ->get();

            // Filter birthdays within next 30 days
            $upcomingBirthdays = $birthdays->filter(function ($user) use ($today, $next30Days) {
                $birthdate = $user->birthdate;
                $birthdayThisYear = now()->setMonth($birthdate->month)->setDay($birthdate->day);
                
                // If birthday already passed this year, use next year
                if ($birthdayThisYear->isPast()) {
                    $birthdayThisYear->addYear();
                }
                
                return $birthdayThisYear->between($today, $next30Days);
            });

            // Filter based on privacy settings
            $visibleBirthdays = $upcomingBirthdays->filter(function ($user) {
                return $user->canViewField('birthdate', Auth::user());
            })->values();

            // Sort by upcoming date
            $sorted = $visibleBirthdays->sortBy(function ($user) {
                $birthdate = $user->birthdate;
                $birthdayThisYear = now()->setMonth($birthdate->month)->setDay($birthdate->day);
                if ($birthdayThisYear->isPast()) {
                    $birthdayThisYear->addYear();
                }
                return $birthdayThisYear->timestamp;
            })->values();

            return response()->json([
                'success' => true,
                'data' => $sorted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch upcoming birthdays',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
