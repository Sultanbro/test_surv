<?php

namespace App\Models\Timetrack;

use Illuminate\Database\Eloquent\Model;

class UserPresence extends Model
{
    protected $table = 'user_presence';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'user_id',
    ];
}
