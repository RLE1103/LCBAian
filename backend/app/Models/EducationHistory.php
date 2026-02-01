<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationHistory extends Model
{
    use HasFactory;

    protected $table = 'education_history';

    protected $fillable = [
        'user_id',
        'level',
        'school_name',
        'year_graduated',
        'awards',
        'is_lcba',
    ];

    protected function casts(): array
    {
        return [
            'year_graduated' => 'integer',
            'is_lcba' => 'boolean',
        ];
    }

    /**
     * Get the user that owns this education record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
