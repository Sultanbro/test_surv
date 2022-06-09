<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trainee extends Model
{

    protected $table = 'b_trainees';

    protected $fillable = [
        'user_id',
        'applied', // принят на работу
        'requested', // запрос на принятие на работу :: возможно лишнее
        'fired', // дата увольнения
        'lead_id', // лид
        'deal_id', // сделка
        'bitrix', // отправлено приглашение в битрикс 0 или 1
    ];
}
