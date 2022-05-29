<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;
use App\User;
use App\UserFine;
use App\ProfileGroupUser;
use App\DayType;
use App\Timetracking;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedSalary;

class Salary extends Model
{
    protected $table = 'salaries';

    protected $dates = ['date'];

    protected $fillable = [
        'amount',
        'note',
        'user_id',
        'date',
        'paid', // аванс
        'bonus', // Бонус, который дается вручную
        'award',  // Бонус, которая посчитала система
        'comment_paid',
        'comment_bonus',
        'comment_award',
    ];

    /**
     * Total salary without subtract fines and avanses
     * 
     * user_types
     * 0 all
     * 1 only working
     * 2 only fired
     */
    public static function getTotal($date, $group_id, $user_types = 0) {

        $month = Carbon::parse($date)->startOfMonth();
        
        $group = ProfileGroup::find($group_id);
        dump($group->name);
        dump('------------');
        dump('~~~~~~~~~~~~');
        $internshipPayRate = $group->paid_internship == 1 ? 0.5 : 0;
        $user_ids = ProfileGroup::employees($group_id, $date, $user_types);

        // // 
        // $users_ids = [];
        // if($group) $users_ids = json_decode($group->users, true);

        // //

        $users = User::withTrashed()
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('ud.is_trainee', 0) 
            ->with([
                'salaries' => function ($q) use ($month) {
                    $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                        ->whereMonth('date', $month->month)
                        ->whereYear('date', $month->year);
                },
                'daytypes' => function ($q) use ($month) {
                    $q->select([
                            'user_id',
                            DB::raw('DAY(date) as day'),
                            'type'
                        ]) 
                        ->whereMonth('date', $month->month)
                        ->whereYear('date', $month->year)
                        ->groupBy('day', 'type', 'user_id', 'date');
                },
                'timetracking' => function ($q) use ($month) {
                    $q->select(['user_id',
                            DB::raw('DAY(enter) as day'),
                            DB::raw('sum(total_hours) as total_hours'),
                            DB::raw('UNIX_TIMESTAMP(enter) as time'),
                        ])
                        ->whereMonth('enter', $month->month)
                        ->whereYear('enter', $month->year)
                        ->groupBy('day', 'enter', 'user_id', 'total_hours', 'time');
                      
                },
            ])
        ->whereIn('users.id', $user_ids)
        ->oldest('users.LAST_NAME')
        ->get();

     

        $all_total = 0;

        $okpi = 0;
        $osal = 0;
        $obon = 0;

        $pgu = ProfileGroupUser::where('group_id', $group_id)
            ->whereMonth('date', $month->month)
            ->whereYear('date', $month->year)
            ->first();
            
        if($user_types == 1 && $pgu) {
            $user_ids = $pgu->assigned;
        } else if($user_types == 2 && $pgu) {
            $user_ids = $pgu->fired;
        } 
      
        foreach ($users as $key => $user) {

           
            // another
            array_push($user_ids, $user->id);

            $groups = $user->inGroups();

            if(count($groups) > 0) {
                if($groups[0]->id != $group_id) {
                    continue;
                }
            }
            ////////////
            $hourly_pay = $user->hourly_pay($month->format('Y-m-d'));
            //$internshipPayRate = $user->internshipPayRate();
            // Вычисление даты принятия
            $user_applied_at = $user->applied_at();

            /////// TTS 

            
            // $tts = Timetracking::whereYear('enter', $month->year)
            //         ->select([
            //             DB::raw('DAY(enter) as day'),
            //             DB::raw('sum(total_hours) as total_hours')
            //         ])
            //         ->whereMonth('enter', $month->month)
            //         ->whereDate('enter', '>=', Carbon::parse($user_applied_at)->format('Y-m-d'))
            //         ->where('user_id', $user->id)
            //         ->groupBy('day')
            //         ->get();
            // dd('x');
            $tts = $user->timetracking
                ->where('time', '>=', Carbon::parse($user_applied_at)->timestamp); 
               // ->groupBy('day');
       
               // dump($user->salaries);
            // $trainee_days = DayType::where('user_id', $user->id)
            //     ->select([
            //         DB::raw('DAY(date) as day'),
            //     ])
            //     ->whereYear('date', $month->year)
            //     ->whereMonth('date', $month->month)
            //     ->whereIn('type', [5,6,7])
            //     ->get();
                
               
            $trainee_days = $user->daytypes->whereIn('type', [5,6,7]);
             

             /////  Рaбочие дни до принятия на работу

            // $tts_before_apply = Timetracking::whereYear('enter', $month->year)
            //     ->select([
            //         DB::raw('DAY(enter) as day'),
            //         DB::raw('sum(total_hours) as total_hours')
            //     ])
            //     ->whereMonth('enter', $month->month)
            //     ->whereDate('enter', '<', Carbon::parse($user_applied_at)->format('Y-m-d'))
            //     ->where('user_id', $user->id)
            //     ->groupBy('day')
            //     ->get();

            $tts_before_apply = $user->timetracking
                ->where('time', '<', Carbon::parse($user_applied_at)->timestamp);
                //->groupBy('day');
                //->get();


                $earnings = []; 
                $hourly_pays = []; 
                $hours = []; 

                $trainings = []; 

                if($user->working_time_id == 1) {
                    $worktime = 8;
                } else {
                    $worktime = 9;
                }

                for ($i = 1; $i <= $month->daysInMonth; $i++) {
                    $d = '' . $i;
                    if(strlen ($i) == 1) $d = '0' . $i;
     


                    $d = '' . $i;
                    if(strlen ($i) == 1) $d = '0' . $i;
                    
                    //count hourly pay 
                    $s = $user->salaries->where('day', $d)->first();
                    $zarplata = $s ? $s->amount : 70000;
                    $working_hours = $user->workingTime ? $user->workingTime->time : 9;
                    $ignore = $user->working_day_id == 1 ? [6,0] : [0];   // Какие дни не учитывать в месяце
                    $workdays = workdays($month->year, $month->month, $ignore);
                
                    $hourly_pay = $zarplata / $workdays / $working_hours;
    
                    $hourly_pays[$i] = round($hourly_pay, 2);

                    ///       
                    $x = $tts->where('day', $i);
                    $y = $tts_before_apply->where('day', $i);
                    $a = $trainee_days->where('day', $i);
                    

                    $earnings[$i] = null;  
                    $hours[$i] = null;  
                    $trainings[$i] = null;

                   

                    if($a->count() > 0) { // день отмечен как стажировка
                        $trainings[$i] = true;
                        $earning = $hourly_pay * $internshipPayRate * $worktime;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($worktime / 2, 1);
                        $hourly_pays[$i] = round($hourly_pay, 2);
                    } else if($x->count() > 0) { // отработанное врея есть
                        $total_hours = $x->sum('total_hours');
                        $earning = $total_hours / 60 * $hourly_pay;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($total_hours / 60, 1);
                        $hourly_pays[$i] = round($hourly_pay, 2);
                    } else if($y->count() > 0) {// отработанное врея есть до принятия на работу
                        $total_hours = $y->sum('total_hours');
                        $earning = $total_hours / 60 * $hourly_pay;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($total_hours / 60, 1);
                        $hourly_pays[$i] = round($hourly_pay, 2);
                       
                    }   
                } 

           
              
            $bonuses = [];
            for ($i = 1; $i <= $month->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
                $x = $user->salaries->where('day', $d)->first();
                if($x && $x->bonus != 0) {
                    $bonuses[$i] = $x->bonus;
                } else {
                    $bonuses[$i] = null;
                }
                
            }

            $award_date = Carbon::createFromFormat('m-Y', $month->month . '-' . $month->year);

            $awards = [];
            for ($i = 1; $i <= $month->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
                $x = ObtainedBonus::onDay($user->id, $award_date->day($i)->format('Y-m-d'));
                if($x != 0) {
                    $awards[$i] = $x;
                } else {
                    $awards[$i] = null;
                }
            }
        
            

                $user->edited_salary = EditedSalary::where('user_id', $user->id)->where('date', $month->format('Y-m-d'))->first();

                //
                $editedKpi = EditedKpi::where('user_id', $user->id)
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->first();

    
                if($editedKpi) {
                    $kpi = $editedKpi->amount;
                } else {
                    $kpi = Kpi::userKpi($user->id, $date);
                }   
             
                $editedBonus = EditedBonus::where('user_id', $user->id)
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->first();

                


            $user_total = 0;
            $total_bonuses = 0;
            $total_salary = 0;
            for($i=1;$i<=$month->daysInMonth;$i++) {
                $total_bonuses += (float)$bonuses[$i] + (float)$awards[$i];
                $total_salary += (float)$earnings[$i];
            }   

            $user_total += $total_salary;

            // dump( $earnings);    
            // dump($user_total);
            if($editedBonus) {
                $user_total += (float)$editedBonus->amount;
                $total_bonuses = (float)$editedBonus->amount;
            } else {
                $user_total += $total_bonuses;
            }
            
            
            $text =  self::addSpace($user->id, 5) . ' • S';
            $text .= self::addSpace($total_salary, 7);
            $text .= ' • B ' ;
            $text .= self::addSpace($total_bonuses, 7);
            $text .= ' • K ' ;
            $text .= self::addSpace($kpi, 7);
            $text .= '      '. $user->LAST_NAME . ' '. $user->NAME;
            

            $avans = self::selectRaw('sum(ROUND(paid,0)) as total')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->where('user_id', $user->id)
                ->first('total')
                ->total;

            $fines = UserFine::selectRaw('sum(ROUND(f.penalty_amount,0)) as total')
                ->leftJoin('fines as f', 'f.id', '=', 'user_fines.fine_id')
                ->where('user_id', $user->id)
                ->whereMonth('day', $month->month)
                ->whereYear('day', $month->year)
                ->where('status', UserFine::STATUS_ACTIVE)
                ->first('total')
                ->total;

                

            $user_total += (float)$kpi;

            $total_must_count = (float)$user_total - $avans - $fines;

            if($total_must_count > 0)  {
                $all_total += $user_total;
                $okpi += $kpi;
                $obon += $total_bonuses;
                $osal += $total_salary;
                dump($text);
            }
            
        }

        if($pgu) {
            $user_ids = array_unique($user_ids);
            $user_ids = array_values($user_ids);
            if($user_types == 1) {
                $pgu->assigned = $user_ids;
                $pgu->save();
            } else if($user_types == 2) {
                $pgu->fired = $user_ids;
                $pgu->save();
            } 
        }
      
        

        dump('SAL ' . $osal);
        dump('BON ' . $obon);
        dump('KPI ' . $okpi);
        return $all_total;
    }

    /**
     * add spaces to money
     */
    private static function addSpace($text, $length) {
        $a = $length - strlen($text);

        for($i=1;$i<=$a;$i++) {
            $text = ' ' . $text;
        }
        return $text;
    }


    public static function salariesTable($user_types, $date, $users_ids, $group_id = 0)
    {
        $date = Carbon::parse($date)->day(1);

        if($user_types == -1) { // one person
            $users = User::withTrashed()
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1);
        }


        if($user_types == 0) {// Действующие
            $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1);
        } 

        if($user_types == 1) {// Уволенные

            $x_users = User::withTrashed()->where('UF_ADMIN', 1)
                ->whereDate('deactivate_date', '>=', $date->format('Y-m-d'))
                ->get(['id','last_group']);

            $fired_users = [];
            foreach($x_users as $d_user) {
                if($d_user->last_group) { 
                    $lg = json_decode($d_user->last_group);
                    if(in_array($group_id, $lg)) {
                        array_push($fired_users, $d_user->id);
                    }
                } 
            }

            $users_ids = $fired_users;

            $users = User::onlyTrashed()->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                 ->where('ud.is_trainee', 0)
                ->where('UF_ADMIN', 1);
        } 

        if($user_types == 2) {// Стажеры
            $users = User::withTrashed()->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('ud.is_trainee', 1)
                ->where('UF_ADMIN', 1);
        } 
        
        $users = $users->with([
            'salaries' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', $date->month)->whereYear('date', $date->year);
            },
            'daytypes' => function ($q) use ($date) {
                $q->select([
                        'user_id',
                        DB::raw('DAY(date) as day'),
                        'type'
                    ]) 
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->groupBy('day', 'type', 'user_id', 'date');
            },
            'fines' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(day, '%e') as date")->whereMonth('day', $date->month)->whereYear('day', $date->year)->where('status', 1);
            },
            'trackHistory' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $date->month)->whereYear('date', $date->year);
            },
            'obtainedBonuses' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $date->month)->whereYear('date', $date->year);
            },
            'timetracking' => function ($q) use ($date) {
                $q->select(['user_id',
                        DB::raw('DAY(enter) as day'),
                        DB::raw('sum(total_hours) as total_hours'),
                        DB::raw('UNIX_TIMESTAMP(enter) as time'),
                    ])
                    ->whereMonth('enter', $date->month)
                    ->whereYear('enter', $date->year)
                    ->groupBy('day', 'enter', 'user_id', 'total_hours', 'time');
            },
        ])->whereIn('users.id', array_unique($users_ids))
            ->get([
                'users.id', 
                'users.EMAIL', 
                'deactivate_date',
                 DB::raw("CONCAT(last_name,' ',name) as full_name"),
                 'user_type',
                'users.DATE_REGISTER',
                 'full_time',
                 'users.working_day_id',
                 'users.working_time_id',
                 'headphones_amount',
                 'headphones_date'
            ]);
        
     
        ///////////////////// 

        $data['users'] = [];
        $data['total_resources'] = 0;

        //me($users);
        foreach ($users as $key => $user) {

            $ugroups = $user->inGroups();

            if(count($ugroups) > 0) {
                if($ugroups[0]->id != $group_id && $user_types != -1) {
                    continue;
                }
            }

            $internshipPayRate = $user->internshipPayRate();
            
            $fines = [];
            $fines_total = 0;
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
                $x = $user->fines->where('date', $d);
                if($x->count() > 0) {
                    $arr = [];
                    foreach($x as $y) {
                        array_push($arr, [
                            $y->name => $y->penalty_amount
                        ]);
                        $fines_total += $y->penalty_amount;
                    }
                    $fines[$i] = $arr;
                } else {
                    $fines[$i] = [];
                }
            }
            
            // Вычисление даты принятия
            $user_applied_at = $user->applied_at();

            /////// TTS 
         
            $tts = $user->timetracking
                    ->where('time', '>=', Carbon::parse($user_applied_at)->timestamp); 
            
            $trainee_days = $user->daytypes->whereIn('type', [5,6,7]);

          
            $tts_before_apply = $user->timetracking
                ->where('time', '<', Carbon::parse($user_applied_at)->timestamp);      
                
            
                

            $earnings = []; 
            $hourly_pays = []; 
            $hours = []; 

            $trainings = []; 

            // worktime hours in day
            if($user->working_time_id == 1) {
                $worktime = 8;
            } else {
                $worktime = 9;
            }

            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
                
                //count hourly pay 
                $s = $user->salaries->where('day', $d)->first();
                $zarplata = $s ? $s->amount : 70000;
                $working_hours = $user->workingTime ? $user->workingTime->time : 9;
                $ignore = $user->working_day_id == 1 ? [6,0] : [0];   // Какие дни не учитывать в месяце
                $workdays = workdays($date->year, $date->month, $ignore);
            
                $hourly_pay = $zarplata / $workdays / $working_hours;

                $hourly_pays[$i] = round($hourly_pay, 2);


                // add to array

                $earnings[$i] = null;  
                $hourly_pays[$i] = null;  
                $hours[$i] = null;  
                $trainings[$i] = null; 

                $x = $tts->where('day', $i);
                $y = $tts_before_apply->where('day', $i);
                $a = $trainee_days->where('day', $i)->first();

                if($a) { // день отмечен как стажировка
                    $trainings[$i] = true;
                    
                    $earning = $hourly_pay * $worktime * $internshipPayRate;
                    $earnings[$i] = round($earning);// стажировочные на пол суммы
                    
                    $hours[$i] = round($worktime / 2, 1);
                } else if($x->count() > 0) { // отработанное врея есть
                    $total_hours = $x->sum('total_hours');
                    $earning = $total_hours / 60 * $hourly_pay;
                    $earnings[$i] = round($earning);
                    $hours[$i] = round($total_hours / 60, 1);
                } else if($y->count() > 0) {// отработанное врея есть до принятия на работу
                    $total_hours = $y->sum('total_hours');
                    $earning = $total_hours / 60 * $hourly_pay;
                    $earnings[$i] = round($earning);
                    $hours[$i] = round($total_hours / 60, 1); 
                }   
            } 

            // headphone price minus
            $headphones_amount = 0;
            $headphones_date = Carbon::parse($user->headphones_date);
            if($user->headphones_amount > 0 &&
                $headphones_date->year == $date->year &&
                $headphones_date->month == $date->month) {
                $headphones_amount = $user->headphones_amount;
            }

             
            $avanses = [];
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
                $x = $user->salaries->where('day', $d)->first();
                if($x && $x->paid != 0) {
                    $avanses[$i] = $x->paid;
                    if($i == 1 && $headphones_amount > 0) $avanses[$i] = (int)$x->paid + $headphones_amount;
                } else {
                    $avanses[$i] = null;
                    if($i == 1 && $headphones_amount > 0) $avanses[$i] = $headphones_amount;
                }
            }

            $bonuses = [];
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
                $x = $user->salaries->where('day', $d)->first();
                if($x && $x->bonus != 0) {
                    $bonuses[$i] = $x->bonus;
                } else {
                    $bonuses[$i] = null;
                }
                
            }

            $award_date = Carbon::createFromFormat('m-Y', $date->month . '-' . $date->year);

            $awards = [];
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;
               
                $x = $user->obtainedBonuses->where('day', $d)->sum('amount');
                if($x > 0) {
                    $awards[$i] = $x;
                } else {
                    $awards[$i] = null;
                }
            }
      
            $user->fine = $fines; 
            $user->trainings = $trainings; 
            $user->fines_total = $fines_total; 
            $user->avanses = $avanses; 
            
            $user->earnings = $earnings; 
            $user->worked_days = Carbon::parse($user_applied_at)->timestamp; 
            $user->hourly_pays = $hourly_pays; 
            $user->hours = $hours; 
  
            $user->bonuses = $bonuses; 
            $user->awards = $awards; 


            $user->edited_salary = EditedSalary::where('user_id', $user->id)->where('date', $date)->first();

            $editedKpi = EditedKpi::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            $user->edited_kpi = null;
            if($editedKpi) {
                $user->kpi = $editedKpi->amount;
                $user->edited_kpi = $editedKpi;
            } else {
                $user->kpi = Kpi::userKpi($user->id, $date);
            }   
                
            $editedBonus = EditedBonus::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            $user->edited_bonus = null;
            if($editedBonus) {
                $user->edited_bonus = $editedBonus;
            } 
            


            $data['users'][] = $user; 
            $data['total_resources'] += $user->full_time == 1 ? 1 : 0.5;  
        }

        $_agrees = array_column($data['users'], 'worked_days');
        array_multisort($_agrees, SORT_DESC, $data['users']); 

        $data['auth_token'] = Auth::user()->remember_token;

        return $data;
    }

}
