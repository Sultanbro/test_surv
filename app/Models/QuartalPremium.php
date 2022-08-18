<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuartalPremium extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'quartal_premiums';

    protected $fillable = [
        'targetable_id',
        'targetable_type',
        'activity_id',
        'title',
        'text',
        'plan',
        'from',
        'to',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
