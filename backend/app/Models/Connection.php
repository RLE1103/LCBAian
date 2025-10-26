<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{
    use HasFactory;

    protected $table = 'connections';
    protected $primaryKey = 'connection_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'alumni_id_from',
        'alumni_id_to',
        'status',
    ];

    public function fromAlumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id_from', 'alumni_id');
    }

    public function toAlumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id_to', 'alumni_id');
    }
}


