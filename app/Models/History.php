<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
{
    use HasFactory;

    protected $table = 'histories';

    protected $fillable = [
        'reference_table',
        'reference_id',
        'actor_id',
        'payload'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'payload',
    ];

    public const DEFAULT = 1;
    public const USER_PROFILE_CHANGED = 2; // check for profile changes

    public function historable()
    {
        return $this->morphTo(__FUNCTION__, 'reference_table', 'reference_id');
    }
}
