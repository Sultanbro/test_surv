<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;

class ProfileGroupUser extends Model
{
    protected $table = 'profile_group_users';

    public $timestamps = false;

    protected $casts = [
        'assigned' => 'array',
        'fired' => 'array'
    ];

    protected $fillable = [
        'group_id', //
        'date', // 
        'assigned', // Работали в этот период
        'fired', // Уволены в этот период 
    ];

    
    
}
