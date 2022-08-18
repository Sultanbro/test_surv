<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
