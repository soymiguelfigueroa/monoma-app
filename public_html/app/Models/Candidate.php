<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    protected $table = 'candidato';

    protected $fillable = [
        'name',
        'source',
        'owner',
        'created_at',
        'created_by',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'owner');
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }
}
