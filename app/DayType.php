<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DayType extends Model
{
    protected $table = 'employee_day_types';

    protected $dates = [
        'date'
    ];

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'admin_id',
		'date',
        'type',
        'email'
    ];

    const DAY_TYPES = [
        'DEFAULT' => 0,
        'HOLIDAY' => 1,
        'ABCENSE' => 2,
        'SICK'    => 3,
        'FIRED'   => 4,
        'TRAINEE' => 5,
        'RETRAIN' => 6,
        'RETURNED' => 7,
        'APPLIED' => 8,
        'ABCENSE_ON_TRAINING' => 9,
    ];

    // 7 Подключился позже
    const DAY_TYPES_RU = [
        0 => 'обычный',
        1 => 'выходной',
        2 => 'прогул',
        3 => 'больничный',
        4 => 'уволенный',
        5 => 'стажер',
        6 => 'переобучение',
        7 => 'подключился позже',
        8 => 'принят на работу',
        9 => 'отсутствовал на обучении',
    ];

    const STAGE_TO_STATUS = [
        'C4:18' => self::DAY_TYPES['TRAINEE'],
        'C4:21' => self::DAY_TYPES['ABCENSE'],
    ];

    public static function getDayTypeWithDay($userId, $date){
        return self::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();
    }
    
}
