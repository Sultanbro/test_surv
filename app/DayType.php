<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $admin_id
 * @property Carbon $date
 * @property string $type
 * @property string $email
 */
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
        'SICK' => 3,
        'FIRED' => 4,
        'TRAINEE' => 5,
        'RETRAIN' => 6,
        'RETURNED' => 7,
        'APPLIED' => 8,
        'ABCENSE_ON_TRAINING' => 9,
        'TRAINEE_PLANNED' => 10,
        'BLANK' => 11,
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
        10 => 'день стажировки планирован',
        11 => 'Без описание'
    ];

    const STAGE_TO_STATUS = [
        'C4:18' => self::DAY_TYPES['TRAINEE'],
        'C4:21' => self::DAY_TYPES['ABCENSE'],
    ];

    public static function getDayTypeWithDay($userId, $date)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', $date)
            ->first();
    }

    public static function markDayAsTrainee(User $user, string|Carbon $date): DayType
    {
        $date = is_string($date) ? $date : $date->format("Y-m-d");
        /** @var DayType */
        return self::query()->firstOrCreate([
            'user_id' => $user->getKey(),
            'type' => DayType::DAY_TYPES['TRAINEE'], // Стажировка
            'email' => '.',
            'date' => $date,
            'admin_id' => 5,
        ]);
    }
}
