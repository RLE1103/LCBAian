<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    use HasFactory;

    protected $primaryKey = 'job_id';

    protected $fillable = [
        'posted_by_admin',
        'user_id',
        'title',
        'description',
        'company_name',
        'location',
        'work_type',
        'required_skills',
        'preferred_skills',
        'industry',
        'experience_level',
        'salary_range',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'posted_by_admin' => 'boolean',
            'created_at' => 'datetime',
            'required_skills' => 'array',
            'preferred_skills' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_id');
    }
}
