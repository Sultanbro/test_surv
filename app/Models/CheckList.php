<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    protected $table = 'check_lists';

    public $timestamps = true;

    protected $casts = [
        'active_check_text' => 'array',
    ];

    protected $fillable = [
        'title',
        'auth_id',
        'auth_name',
        'auth_last_name',
        'count_view',
        'item_type',
        'item_id',
    ];
}
