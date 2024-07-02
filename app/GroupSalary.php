<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use App\User;
use App\ProfileGroup;

/**@brief test
 * 
 */
class GroupSalary extends Model
{
    protected $table = 'group_salaries';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'total',
        'type',
        'date',
    ];

    // type
    CONST WORKING = 1;
    CONST FIRED = 2;

    /**
     * все суммы начислений по группам 
     * для страницы Начисления
     */
    public static function getAccruals($date)
    {
        $accruals = [0=>0];

        $groups = ProfileGroup::where('active', 1)->select('id')->get();
        foreach($groups as $group) {
            $val = self::where('group_id', $group->id)
                ->where('date', $date)
                ->get()
                ->sum('total');
            
            $val = strrev(implode(',', str_split(strrev($val), 3)));
            $accruals[$group->id] = $val;
        }
        
        return $accruals;
    }
}
