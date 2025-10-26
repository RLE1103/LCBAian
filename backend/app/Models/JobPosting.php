<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosting extends Model
{
    use HasFactory;

    protected $table = 'job_postings';
    protected $primaryKey = 'job_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'employer_name',
        'job_title',
        'description',
        'requirements',
        'posted_date',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'posted_date' => 'datetime',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_id', 'job_id');
    }

    public function applicants(): BelongsToMany
    {
        return $this->belongsToMany(Alumni::class, 'applications', 'job_id', 'alumni_id', 'job_id', 'alumni_id')
            ->withPivot(['status', 'date_applied'])
            ->withTimestamps();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}


