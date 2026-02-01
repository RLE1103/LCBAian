<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'email',
        'password',
        'role',
        'profile_picture',
        'headline',
        'bio',
        'birthdate',
        'skills',
        'cluster_group',
        'career_interests',
        'current_job_title',
        'industry',
        'experience_level',
        // Contact & Socials
        'linkedin_url',
        'portfolio_url',
        // Location
        'location',
        'city',
        'country',
        // Career
        'employment_status',
        'employment_sector',
        'years_of_experience',
        'salary_range',
        // Career Preferences
        'work_setup_preferences',
        'employment_type_preferences',
        'industries_of_interest',
        // Education
        'program',
        'batch',
        'highest_educational_attainment',
        // LCBA Employee/Faculty
        'is_lcba_employee_faculty',
        'lcba_employee_id',
        'lcba_verification_status',
        // Privacy
        'privacy_settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'birthdate' => 'date',
            'skills' => 'array',
            'career_interests' => 'array',
            'work_setup_preferences' => 'array',
            'employment_type_preferences' => 'array',
            'industries_of_interest' => 'array',
            'privacy_settings' => 'array',
        ];
    }

    // Relationships
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function mentorshipsAsMentor(): HasMany
    {
        return $this->hasMany(Mentorship::class, 'mentor_id');
    }

    public function mentorshipsAsMentee(): HasMany
    {
        return $this->hasMany(Mentorship::class, 'mentee_id');
    }

    public function communitiesCreated(): HasMany
    {
        return $this->hasMany(Community::class, 'created_by');
    }

    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'community_members')->withTimestamps();
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'alumni_badges')->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminLog::class);
    }

    public function educationHistory(): HasMany
    {
        return $this->hasMany(EducationHistory::class);
    }

    public function reportsSubmitted(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_user_id');
    }

    public function reportsReviewed(): HasMany
    {
        return $this->hasMany(Report::class, 'reviewed_by_admin_id');
    }

    // Helper methods
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        
        $name .= ' ' . $this->last_name;
        
        if ($this->suffix) {
            $name .= ' ' . $this->suffix;
        }
        
        return $name;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    public function isAlumni(): bool
    {
        return $this->role === 'alumni';
    }

    public function isFaculty(): bool
    {
        return $this->role === 'faculty';
    }

    /**
     * Get privacy setting for a specific field
     */
    public function getPrivacySetting(string $field): string
    {
        $settings = $this->privacy_settings ?? [];
        return $settings[$field] ?? 'alumni_only'; // Default to alumni only
    }

    /**
     * Check if a viewer can see a specific field based on privacy settings
     */
    public function canViewField(string $field, ?User $viewer): bool
    {
        // Admin can always see everything
        if ($viewer && $viewer->isAdmin()) {
            return true;
        }

        // User can always see their own fields
        if ($viewer && $viewer->id === $this->id) {
            return true;
        }

        $privacy = $this->getPrivacySetting($field);

        switch ($privacy) {
            case 'public':
                return true;
            case 'alumni_only':
                return $viewer && $viewer->isAlumni();
            case 'connections_only':
                // TODO: Implement connection checking when connections feature is ready
                return false;
            case 'admin_only':
                return false;
            default:
                return false;
        }
    }
}