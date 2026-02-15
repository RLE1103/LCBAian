<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $primaryKey = 'badge_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'icon_path',
        'criteria',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'alumni_badges', 'badge_id', 'user_id')->withPivot('date_awarded');
    }
}
