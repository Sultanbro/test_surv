<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Analytics\Activity;
use App\Models\Analytics\ActivityTotal; // Нужно удалить. Только использовался 04.2021
use App\QualityRecordMonthlyStat;
use Carbon\Carbon;
use App\ProfileGroup;
use App\Models\Analytics\KpiIndicator;
use App\Models\Analytics\IndividualKpiIndicator;
use App\Models\Analytics\IndividualKpi;

class AnalyticsSettingsIndividually extends Model
{
    protected $table = 'analytics_settings_individually';

    protected $fillable = [
        'data', 'date', 'group_id', 'user_id', 'type', 'employee_id'
    ];

    /**
     * Прогресс выполнения активности сотрудником для вычисления KPI
     */
    public static function getActivityProgress(int $user_id, int $group_id, Activity $activity, string $date = '', $return_value_and_percent = false)
    {
      
        if($date == '') {
            $date = Carbon::now()->day(1)->format('Y-m-d');
        }
        
        $user = User::withTrashed()->find($user_id);
        
        if($group_id == 0) {
            $ignore = [0];
        } else {
            $ignore = ProfileGroup::find($group_id)->workdays == 5 ? [0,6] : [0];
        }
        

        if($group_id == 53 && Carbon::parse($date)->year == 2022 && Carbon::parse($date)->month == 3) {
            $workdays = 19;
        } else if($group_id == 57  && Carbon::parse($date)->year == 2022 && Carbon::parse($date)->month == 3) {
            $workdays = 22;
        } else {
            $workdays = self::workdays(Carbon::parse($date)->year, Carbon::parse($date)->month, $ignore);
        }
        
        if($group_id == 48) {
            $records = self::where([
                'employee_id' => $user_id,
                'date' => $date,
            ])->first();
        } else {
            $records = self::where([ 
                'employee_id' => $user_id,
                'date' => $date,
                'type' => $activity->id 
            ])->first();
        }
        
        

        /////////////////////////
        if($activity->type == 'quality') {
           
            $act = Activity::find($activity->id);
           
            $at = QualityRecordMonthlyStat::where([
                'month' => date('m', strtotime($date)),
                'year' => date('Y', strtotime($date)),
                'user_id' => $user_id,
            ])->first();

            //me($act->daily_plan);
            $total = 0;
            if($at) {
                $total = $at->total;
                $result = $at->total / $act->daily_plan * 100;
                
            } else {
                $result = 0;
            }
            
            
            if($return_value_and_percent) {
                return [
                    'value' => (int)$total,
                    'percent' => number_format($result, 2),
                ];
            } else {

                return $result;
            }
        }
     
        if($group_id == 48) { 
         
            $total = 0;
            $result = 0;
            $indexes = [
                22 => 0,
                45 => 1,
                49 => 2,
                50 => 3,
                51 => 4,
                52 => 5,
                53 => 6,
                54 => 7,
            ]; // activity => index
    
          
            if(!array_key_exists($activity->id, $indexes)) {
                $result = 0;
                //memp($activity);
             
            } else {
        
                $asi = AnalyticsSettingsIndividually::where([
                    'date' => $date,
                    'employee_id' => $user_id,
                ])->first();
                
                if($asi) { 
                    $data = json_decode($asi->data, true);
                    
                    
                    $index = $indexes[$activity->id];

                    try {
                        for($i = 1; $i <= 31;$i++) {
                            if(array_key_exists($index, $data) 
                            && array_key_exists($i, $data[$index])) {

                                $total += (float)$data[$index][$i];
                            }   
                        }
                    } catch(\Throwable $e) {
                        
                    }
                    
                    $daily_plan = $activity->daily_plan;

                    if($activity->plan_unit == 'minutes') {
                        if($user && $user->full_time == 0) $daily_plan = $daily_plan / 2;
                        $result = $total / ((float)$daily_plan * $workdays) * 100;

                        // dump($daily_plan);
                        // dump($workdays);
                        // dump($result);
                        // dd($total);
                    } 

            
                    if($activity->plan_unit == 'less_sum') {
                        $result = ($activity->daily_plan - $total) / $activity->daily_plan * 100;
                    } 

                    if($activity->plan_unit == 'percent') {
                        $result = $total / ((float)$activity->daily_plan) * 100;
                        $total = $result;
                    }

                    if($activity->plan_unit == 'less_avg') {
                        $result = ((float)$activity->daily_plan) / $total * 100;
                        $total = $result;
                    } 

                    // if(auth()->id() == 11080) dump($activity->daily_plan); 
                    // if(auth()->id() == 11080) dump($workdays); 
                    // if(auth()->id() == 11080) dump($total); 
                    // if(auth()->id() == 11080) dump($result); 

                  
                    
                } 
            }
        

            if($return_value_and_percent) {
                return [
                    'value' => $total - (int)$total > 0 ? number_format($total, 2) : (int)$total,
                    'percent' => number_format($result, 2),
                ];
            } else {
                return $result;
            }

        }
       
        ///////////////////////////////

        $total = 0;

        $time_rate = $user && $user->full_time == 1 ? 1 : 0.5;

        if($records) {
            
            $data = json_decode($records->data, true);
            
         
            $count = 0;
            for($i = 1; $i <= 31;$i++) {
                if(array_key_exists($i, $data) && (float)$activity->daily_plan != 0) {
                    $total += (float)$data[$i];
                    if((float)$data[$i] > 0) {
                        $count++;
                    }
                }   
            }

            if((float)$activity->daily_plan != 0) {
                
                if($activity->plan_unit == 'minutes') {
                    $result = $total / ((float)$activity->daily_plan * $workdays * $time_rate) * 100;
                    
                } 

                if($activity->plan_unit == 'less_sum') {
                    $result = ($activity->daily_plan - $total) / $activity->daily_plan * 100;
                }
                
                if($activity->plan_unit == 'less_avg') {
                    if($count > 0) {
                        $avg = $total / $count;
                        $total = $avg;
                        $result =  ((float)$activity->daily_plan) / $avg * 100;
                    } else {
                        $result = 0;
                    }
                }

                if($activity->plan_unit == 'percent') {
                    if($count > 0) {
                        $avg = $total / $count;
                        $result = $avg / ((float)$activity->daily_plan) * 100;
                    } else {
                        $result = 0;
                        $avg = 0;
                    }
                    $total = $avg;
                }
                
                $result =(float)$result;

            } else {
                $result = 0;
                if($activity->plan_unit == 'less_sum') {
                    $result = 100;
                }
            }
        } else {
            $result = 0;
            if($activity->plan_unit == 'less_sum') {
                $result = 100;
            }
        }

        
        if($return_value_and_percent) {
            return [
                'value' => $total - (int)$total > 0 ? number_format($total, 2) : (int)$total,
                'percent' => number_format($result, 2),
            ];
        } else {
            return $result;
        }
    }

    /**
     * Only for kaspi prosrochka get bonus
     */
    public static function getBonus(int $user_id, String $date) {
        $record = self::where([
            'date' => $date, // Y-m-d
            'employee_id' => $user_id,
            'group_id' => 42, // kaspi pros
            'type' => 13, // gathering activity id
        ])->first();

        $bonus = 0;  
        if($record) {
            $data = json_decode($record->data, true);
            if(array_key_exists('plan', $data)) {
                $bonus = (int)$data['plan'];
            }
        } 

        return $bonus;

    }

    /**
     * Рабочие дни
     */
    public static function workdays($year, $month, $ignore) {
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {
            if (in_array(date("w", $counter), $ignore) == false) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        return $count;
    }

    /**
     * Обший прогресс выполнения активности, для индивидуального KPI
     */
    public static function getTotalActivityProgress(int $user_id, Activity $activity, string $date = '', $return_value_and_percent = false)
    {
        if($date == '') {
            $date = Carbon::now()->day(1)->format('Y-m-d');
        } 
    
        //get users

        if($activity->group_id == 48) {
            $user_ids = self::where([
                'date' => $date,
            ])
            ->whereNotNull('employee_id')
            ->get(['employee_id'])
            ->pluck('employee_id')
            ->toArray();
        } else {
            $user_ids = self::where([
                'date' => $date,
                'type' => $activity->id 
            ])
            ->whereNotNull('employee_id')
            ->get(['employee_id'])
            ->pluck('employee_id')
            ->toArray();
        }
        
        //foreach users

            

        $total = 0;
        $count = 0;
        
        foreach ($user_ids as $key => $_user_id) {
            $_total = self::getActivityProgress($_user_id, $activity->group_id, $activity, $date, true);
          
            
           // me($date);
            
            if($_total['value'] > 0) {
                $total += (float)$_total['value'];
                $count++;
            }
        }
        

        
        // get kpi indicators

        // formulas on plan unit

        $result = 0;
        $workdays = self::workdays(Carbon::parse($date)->year, Carbon::parse($date)->month, [0]);

        // ind kpi 
        $ind_kpi = IndividualKpiIndicator::where('user_id', $user_id)
            ->where('activity_id', $activity->id)
            ->first();

            

        $daily_plan = 0;
        $plan_unit = $activity->plan_unit;
        if($ind_kpi) {
            $daily_plan = $ind_kpi->plan;
        }
        
        // memp($plan_unit);
        // memp($daily_plan);
        // me($total);

        // calc 
        if((float)$daily_plan != 0) {
                
            if($plan_unit == 'minutes') {
                // memp($total);
                // memp($daily_plan);
                // memp($workdays);
                // memp($daily_plan * $workdays);
                $result = $total / ((float)$daily_plan) * 100;
            } 

            if($plan_unit == 'less_sum') {
                $result = ($daily_plan - $total) / $daily_plan * 100;
            }
            
            if($plan_unit == 'less_avg') {
                if($count > 0) {
                    $avg = $total / $count;
                    $total = $avg;
                    $result =  ((float)$daily_plan) / $avg * 100;
                } else {
                    $result = 0;
                }
            }

            if($plan_unit == 'percent') {
                if($count > 0) {
                    $avg = $total / $count;
                    $result = $avg / ((float)$daily_plan) * 100;
                } else {
                    $result = 0;
                    $avg = 0;
                }
                $total = $avg;
            }
            
            $result =(float)$result;

        } else {
            $result = 0;
            if($plan_unit == 'less_sum') {
                $result = 100;
            }
        }
        
        
        return $return_value_and_percent ? [
            'value' => (int)$total > 0 ? number_format($total, 2) : (int)$total,
            'percent' => number_format($result, 2),
        ] : $result;
    }
}
