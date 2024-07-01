<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class PositionDescription extends Model
{
    protected $table = 'position_descriptions';

    public $timestamps = false;

    protected $fillable = [
        'position_id',
        'require',
        'actions',
        'time',
        'salary',
        'knowledge',
        'next_step',
        'show',
    ];
}
