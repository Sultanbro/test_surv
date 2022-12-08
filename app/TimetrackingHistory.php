<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimetrackingHistory extends Model
{
    protected $casts = [
        'timezone' => Setting::TIMEZONES[6]
    ];

    protected $table = 'timetracking_history';

    protected $fillable = [
        'user_id',
        'author_id',
        'author',
        'date',
        'description',
    ];
}
