<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $dates = ['admins'];
    
    protected $casts = [
        'users' => 'array',
    ];

    protected $fillable = [
        'owner_id',
        'users',
    ];
}
