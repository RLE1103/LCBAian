<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillsTaxonomy extends Model
{
    use HasFactory;

    protected $table = 'skills_taxonomy';

    protected $fillable = [
        'name',
        'category',
        'usage_count'
    ];

    protected $casts = [
        'usage_count' => 'integer'
    ];

    /**
     * Increment usage count when skill is used
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get skills by category
     */
    public static function getByCategory(?string $category = null)
    {
        $query = self::query();
        if ($category) {
            $query->where('category', $category);
        }
        return $query->orderBy('usage_count', 'desc')->orderBy('name')->get();
    }

    /**
     * Search skills by name
     */
    public static function search(string $query, int $limit = 20)
    {
        return self::where('name', 'like', "%{$query}%")
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }
}
