<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     * Note: Admin role is not available for public registration
     * Only 'alumni' role is allowed
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:alumni',
            'program' => 'nullable|string|max:255',
            'batch' => 'nullable|integer|min:1990|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'program' => $request->program,
            'batch' => $request->batch,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'role' => $user->role
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'suffix' => 'nullable|string|in:Jr.,Sr.,II,III,IV',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            // Contact & Socials
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            // Education
            'program' => 'nullable|string|max:255',
            'batch' => 'nullable|string|max:20',
            // Location
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            // Career
            'current_job_title' => 'nullable|string|max:100',
            'industry' => 'nullable|string|max:100',
            'experience_level' => 'nullable|string|max:50',
            'employment_status' => 'nullable|in:employed_full_time,employed_part_time,self_employed,in_study,unemployed_looking,unemployed_not_looking',
            'years_of_experience' => 'nullable|in:0-2,3-5,6-10,10+',
            'salary_range' => 'nullable|in:prefer_not_to_say,20000-39999,40000-59999,60000-79999,80000-99999,100000+',
            // Skills (array of standardized skill names)
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            // Career Preferences
            'work_setup_preferences' => 'nullable|array',
            'work_setup_preferences.*' => 'in:on_site,hybrid,remote',
            'employment_type_preferences' => 'nullable|array',
            'employment_type_preferences.*' => 'in:full_time,part_time,contract,internship',
            'industries_of_interest' => 'nullable|array',
            'industries_of_interest.*' => 'string|max:100',
            // Visibility Settings
            'visibility_settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update skills and increment usage counts
        if ($request->has('skills')) {
            $skills = $request->input('skills', []);
            foreach ($skills as $skillName) {
                $skill = \App\Models\SkillsTaxonomy::firstOrCreate(
                    ['name' => $skillName],
                    ['category' => null]
                );
                $skill->incrementUsage();
            }
        }

        $user->update($request->only([
            'first_name', 'last_name', 'email', 'headline', 'bio',
            'public_email', 'linkedin_url', 'portfolio_url',
            'program', 'batch',
            'city', 'country',
            'current_job_title', 'industry', 'experience_level',
            'employment_status', 'years_of_experience', 'salary_range',
            'skills',
            'work_setup_preferences', 'employment_type_preferences', 'industries_of_interest'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Create admin user (for system administrators only)
     * This method should be used by existing admins to create new admin accounts
     */
    public function createAdmin(Request $request)
    {
        // Check if the requesting user is an admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Only admins can create admin accounts.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'program' => 'nullable|string|max:255',
            'batch' => 'nullable|integer|min:1990|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Force admin role
            'program' => $request->program,
            'batch' => $request->batch,
        ]);

        return response()->json([
            'message' => 'Admin user created successfully',
            'user' => $user
        ], 201);
    }
}
