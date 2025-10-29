<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommunityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Community::with(['creator', 'members']);

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Category filter
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }

            // Visibility filter
            if ($request->has('visibility') && $request->visibility) {
                $query->where('visibility', $request->visibility);
            }

            $communities = $query->orderBy('created_at', 'desc')
                               ->get();

            return response()->json([
                'success' => true,
                'data' => $communities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $community = Community::with(['creator', 'members', 'posts'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $community
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'description' => 'required|string',
                'category' => 'nullable|string|max:50',
                'visibility' => 'nullable|in:public,private',
                'tags' => 'nullable|array'
            ]);

            $community = Community::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category ?? 'general',
                'visibility' => $request->visibility ?? 'public',
                'tags' => $request->tags ? json_encode($request->tags) : null,
                'created_by' => $request->user()->id
            ]);

            // Add creator as member
            $community->members()->attach($request->user()->id);

            return response()->json([
                'success' => true,
                'data' => $community->load(['creator', 'members'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function join(Request $request, $id): JsonResponse
    {
        try {
            $community = Community::findOrFail($id);
            $user = $request->user();

            // Check if user is already a member
            if ($community->members()->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already a member of this community'
                ], 400);
            }

            $community->members()->attach($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined the community'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function leave(Request $request, $id): JsonResponse
    {
        try {
            $community = Community::findOrFail($id);
            $user = $request->user();

            $community->members()->detach($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Successfully left the community'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
