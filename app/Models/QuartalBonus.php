<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuartalBonus extends Model
{
    protected $table = 'quartal_bonuses';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'auth_id',
        'quartal',
        'sum',
        'year',
        'text',
    ];

    CONST FIRST_QUARTER = 1;
    CONST SECOND_QUARTER = 2;
    CONST THIRD_QUARTER = 3;
    CONST FOURTH_QUARTER = 4;

}
