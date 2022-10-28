<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\User;

/**@brief test
 * 
 */
class EstimateGrade extends Model
{
    protected $table = 'estimate_grades';

    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'user_id', 
        'about_id', // руковод или стар специалист
        'date', // месяц Y-m-d
        'grade', // оценка от 1 до 10
        'text', // почему вы поставили
        'minus', // колонка для противо положной характеристики
    ];
}
