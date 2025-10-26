<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumni';
    protected $primaryKey = 'alumni_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'course',
        'graduation_year',
        'current_job',
        'industry',
        'bio',
        'profile_photo',
    ];

    protected function casts(): array
    {
        return [
            'graduation_year' => 'integer',
            'password' => 'hashed',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'alumni_id', 'alumni_id');
    }

    public function jobsApplied(): BelongsToMany
    {
        return $this->belongsToMany(JobPosting::class, 'applications', 'alumni_id', 'job_id', 'alumni_id', 'job_id')
            ->withPivot(['status', 'date_applied'])
            ->withTimestamps();
    }

    public function connectionsFrom(): HasMany
    {
        return $this->hasMany(Connection::class, 'alumni_id_from', 'alumni_id');
    }

    public function connectionsTo(): HasMany
    {
        return $this->hasMany(Connection::class, 'alumni_id_to', 'alumni_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'alumni_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id', 'alumni_id');
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'user');
    }
}


