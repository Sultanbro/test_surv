<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDeletePlan extends Model
{
    // Таблица для увольнения с задержкой (с отработкой)
    protected $table = 'user_delete_plans'; 

    protected $fillable = [
        'user_id', // сотрудник
        'delete_time', // дата увольнения
        'executed', // Выполнено или нет
    ];
}