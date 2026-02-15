<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_user_id',
        'reported_entity_type',
        'reported_entity_id',
        'reason',
        'description',
        'status',
        'reviewed_by_admin_id',
        'admin_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Get the user who reported this
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    /**
     * Get the admin who reviewed this report
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }

    /**
     * Get the reported entity (polymorphic-like relationship)
     * Note: This is a manual implementation since we're using enum for type
     */
    public function getReportedEntity()
    {
        switch ($this->reported_entity_type) {
            case 'job_post':
                if (!Schema::hasTable('job_posts')) return null;
                return JobPost::where('job_id', $this->reported_entity_id)->first();
            case 'user':
                if (!Schema::hasTable('users')) return null;
                return User::find($this->reported_entity_id);
            case 'post':
                if (!Schema::hasTable('posts')) return null;
                return Post::where('post_id', $this->reported_entity_id)->first();
            case 'comment':
                if (!Schema::hasTable('comments')) return null;
                return Comment::where('comment_id', $this->reported_entity_id)->first();
            case 'event':
                if (!Schema::hasTable('events')) return null;
                return Event::find($this->reported_entity_id);
            default:
                return null;
        }
    }
}
