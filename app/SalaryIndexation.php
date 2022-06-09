<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Zarplata;
use App\User;
use App\UserDescription;

class SalaryIndexation extends Model
{
    protected $table = 'salary_indexations';

    protected $fillable = [
        'user_id',
        'step',
    ];

    CONST STEPS = [
        0 => 'Новичок',
        1 => '90 дней',
        2 => '180 дней',
        3 => '270 дней',
        4 => '360 дней',
        5 => '1 год 90 дней',
        6 => '1 год 180 дней',
        7 => '1 год 270 дней',
        8 => '2 года',
    ];
}
