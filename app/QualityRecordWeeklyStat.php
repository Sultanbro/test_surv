<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\User;

class QualityRecordWeeklyStat extends Model
{
    public $timestamps = false;

    protected $table = 'quality_record_weekly_stats';

    protected $fillable = [
        'day',
        'month',
        'year',
        'total',
        'user_id',
        'group_id',
    ];


    public static function table($user_ids, $date) {
        $date = Carbon::parse($date);

        $users = \DB::table('users')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->whereIn('users.id', $user_ids)
            ->where('is_trainee', 0)
            ->orderBy('last_name', 'asc')
            ->select(['users.id','users.last_name', 'users.name'])
            ->get();

       
        $items = [];
        $items2 = [];
        
        // CREATE WEEKS ARRAY
        $weeks = self::weeksArray($date->month, $date->year);
      
        foreach($users as $user) {
            $item = [];

            $item['name'] = $user->last_name. ' ' . $user->name;
            $item['id'] = $user->id;
            
            // FETCHING WEEKS DATA
            $week_totals = self::where([
                    'user_id' => $user->id,
                    'month' => $date->month,
                    'year' => $date->year,
                ])->get();
            
            foreach($week_totals as $week) {
                $item[$week->day] = $week->total;
            }

            ///// COUNT WEEKS TOTALS
            $item['total'] = 0;
            $actual_weeks = 0;
            
            foreach($weeks as $key => $value) {
                $avg = 0;
                $count = 0;
                
                foreach($value as $val){
                    if(isset($item[$val])) {
                        $avg += $item[$val];
                        if($item[$val] >= 0) $count++;
                    }
                }
                
                if($count > 0) {
                    $result = round($avg / $count, 2);
                    $item['avg'.$key] = $result;
                    $item['total'] += $result;
                    $actual_weeks++;
                } 
            }

            if($actual_weeks > 0) {
                $item['total'] = round($item['total'] / $actual_weeks, 2);
            }
            if($item['total']) array_push($items2, $item);
            array_push($items, $item);
        }

        //dd(collect($items2)->pluck('id')->toArray());

        return $items;
    }


    /**
     * Create weeks array with days 
     */
    private static function weeksArray($month, $year) {
        $weeks = [];
        $week_number = 1;
        $week = [];
        $daysInMonth = Carbon::createFromFormat('m-Y', $month . '-' . $year)->daysInMonth;

        for($d=1;$d<=$daysInMonth;$d++) {
            
            array_push($week, (int)$d); 
            
            if(Carbon::createFromFormat('d-m-Y', $d . '-' . $month . '-' . $year)->dayOfWeek == Carbon::SUNDAY) {
                $weeks[$week_number] = $week;
                $week = [];
                $week_number++;
            }

            if($d == $daysInMonth){
                $weeks[$week_number] = $week;
            }
        }

        return $weeks;
    }
}
