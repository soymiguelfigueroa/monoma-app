<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    protected $table = 'candidato';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'source',
        'owner',
        'created_at',
        'created_by',
    ];
}
