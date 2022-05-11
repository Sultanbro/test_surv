<?php

namespace App;

use App\TimetrackingHistory;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserDeletePlan extends Model
{
    protected $table = 'b_user_delete_plans'; // Таблица для увольнения с задержкой (с отработкой)

    protected $fillable = [
        'user_id', // сотрудник
        'delete_time', // дата увольнения
        'executed', // Выполнено или нет
    ];

   
}
