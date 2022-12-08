<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimetrackingHistory extends Model
{
    protected $table = 'timetracking_history';

    protected $fillable = [
        'user_id',
        'author_id',
        'author',
        'date',
        'description',
    ];
}
