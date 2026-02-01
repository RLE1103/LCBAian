<?php

namespace App\Http\Controllers;

use App\Models\SkillsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SkillsTaxonomyController extends Controller
{
    /**
     * Get all skills (for autocomplete)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $limit = $request->get('limit', 20);

            if ($query) {
                $skills = SkillsTaxonomy::search($query, $limit);
            } else {
                $skills = SkillsTaxonomy::orderBy('usage_count', 'desc')
                    ->orderBy('name')
                    ->limit($limit)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'category' => $skill->category
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get skills by category
     */
    public function byCategory(Request $request): JsonResponse
    {
        try {
            $category = $request->get('category');
            $skills = SkillsTaxonomy::getByCategory($category);

            return response()->json([
                'success' => true,
                'data' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create or update a skill (for admin use)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'category' => 'nullable|string|max:50'
            ]);

            $skill = SkillsTaxonomy::firstOrCreate(
                ['name' => $validated['name']],
                ['category' => $validated['category'] ?? null]
            );

            return response()->json([
                'success' => true,
                'data' => $skill
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
