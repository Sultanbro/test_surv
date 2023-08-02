<?php

namespace App;

use App\Models\Analytics\Activity;
use App\Models\Analytics\UserStat;
use App\Models\WorkChart\WorkChartModel;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;
use Auth;
use App\UserFine;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedBonus;
use App\Models\Admin\EditedSalary;
use App\Service\Department\UserService;
use Exception;

class Salary extends Model
{
    CONST ALL_USERS = 0;
    CONST WORKING_USERS = 1;
    CONST FIRED_USERS = 2;

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
    public static function getTotal($date, $group_id, $user_types = self::ALL_USERS)
    {
        $month = Carbon::parse($date)->startOfMonth();

        $group = ProfileGroup::find($group_id);

        dump($group->name);
        dump('------------');
        dump('~~~~~~~~~~~~');

        $internshipPayRate = $group->paid_internship == 1 ? 0.5 : 0;
        // $user_ids = ProfileGroup::employees($group_id, $date, $user_types);

        $working = (new UserService)->getEmployees($group_id, $date);
        $working = collect($working)->pluck('id')->toArray();

        $fired = (new UserService)->getFiredEmployees($group_id, $date);
        $fired = collect($fired)->pluck('id')->toArray();

        $user_ids = [];
        if($user_types == self::ALL_USERS)  {
            $user_ids = array_merge($working, $fired);
        } else if($user_types == self::WORKING_USERS) {
            $user_ids = $working;
        } else if($user_types == self::FIRED_USERS) {
            $user_ids = $fired;
        }

        $users = self::getUsersData($month, $user_ids);

        $all_total = 0;

        $okpi = 0;
        $osal = 0;
        $obon = 0;

        foreach ($users as $key => $user) {
            if($user->user_description && $user->user_description->is_trainee == 0) {

            } else {
                continue;
            }

            // another
            array_push($user_ids, $user->id);

            $groups = $user->inGroups();

            if(count($groups) > 0 && $groups[0]->id != $group_id) {
                continue;
            }

            $hourly_pay = $user->hourly_pay($month->format('Y-m-d'));
            dump('hourly_pay '.$hourly_pay);

            // Вычисление даты принятия
            $user_applied_at = $user->applied_at();

            $tts = $user->timetracking
                ->where('time', '>=', Carbon::parse($user_applied_at)->timestamp);

            $trainee_days = $user->daytypes->whereIn('type', [5,6,7]);

            $tts_before_apply = $user->timetracking
                ->where('time', '<', Carbon::parse($user_applied_at)->timestamp);

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

                //count hourly pay
                $s = $user->salaries->where('day', $d)->first();
                $zarplata = $s ? $s->amount : 70000;

                $schedule = $user->schedule();
                $lunchTime = 1;
                $working_hours = $working_hours = max($schedule['end']->diffInHours($schedule['start']) - $lunchTime, 0);

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


            // if($user->id == 18392) dd($earnings);

            for($i=1;$i<=$month->daysInMonth;$i++) {
                $total_bonuses += (float)$bonuses[$i] + (float)$awards[$i];
                $total_salary += (float)$earnings[$i];
            }

            $total_bonuses += $user->testBonuses->sum('amount');

            $user_total += $total_salary;

            dump($earnings);
            dump($user_total);
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
            $text .= ' • T ' ;
            $text .= self::addSpace($kpi + $total_bonuses + $total_salary, 10);
            $text .= '      '. $user->last_name . ' '. $user->name;


            $avans = self::selectRaw('sum(ROUND(paid,0)) as total')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->where('user_id', $user->id)
                ->first('total')
                ->total;

            $fines = \DB::table('user_fines')
                ->selectRaw('sum(ROUND(f.penalty_amount,0)) as total')
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


        dump('SAL ' . $osal);
        dump('BON ' . $obon);
        dump('KPI ' . $okpi);
        return $all_total;
    }


    public static function getUsersData($month, $user_ids){
        return User::withTrashed()
            //->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->with([
                'user_description',
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
                'testBonuses' => function ($q) use ($month) {
                    $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $month->month)->whereYear('date', $month->year);
                },
            ])
            ->whereIn('users.id', $user_ids)
            ->oldest('users.last_name')
            ->get();
    }

    public static function getUsersDataV2($month, $user_ids){
        return User::withTrashed()
            //->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->with([
                'groups' => function ($q) use ($month) {
                    $q->with('workChart')
                        ->where([
                            ['status', 'active'],
                            ['is_head', false]
                        ])
                        ->whereNull('to');
                },
                'zarplata',
                'workingTime',
                'user_description',
                'workChart',
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
                'testBonuses' => function ($q) use ($month) {
                    $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                        ->whereMonth('date', '=', $month->month)
                        ->whereYear('date', $month->year);
                },
                'kpi_obtained_bonuses' => function ($q) use ($month) {
                    $q->select(['user_id', 'amount'])
                        ->whereYear('date', $month->year)
                        ->whereMonth('date', $month->month)
                        ->get();
                },
                'edited_salaries' => function ($q) use ($month) {
                    $q->select(['user_id', 'amount'])
                        ->whereYear('date', $month->year)
                        ->whereMonth('date', $month->month)
                        ->get();
                },
                'edited_kpi' => function ($q) use ($month) {
                    $q->select(['user_id', 'amount'])
                        ->whereYear('date', $month->year)
                        ->whereMonth('date', $month->month)
                        ->get();
                },
                'saved_kpi' => function ($q) use ($month) {
                    $q->select(['user_id', 'total'])
                        ->whereYear('date', $month->year)
                        ->whereMonth('date', $month->month)
                        ->get();
                },
                'edited_bonuses' => function ($q) use ($month) {
                    $q->select(['user_id', 'amount'])
                        ->whereYear('date', $month->year)
                        ->whereMonth('date', $month->month)
                        ->get();
                },
                'fines' => function ($q) use ($month) {
                    $q->whereYear('day', $month->year)
                        ->whereMonth('day', $month->month)
                        ->where('status', 1)
                        ->get();
                },
            ])
            ->whereIn('users.id', $user_ids)
            ->oldest('users.last_name')
            ->get();
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

    /**
     * Create salaries array
     * with days as keys
     * from 1 to 31 day
     *
     * @param array $data
     * @return array
     */
    public static function getSalaryForDays(array $data) : array
    {
        $date     = Carbon::parse($data['date']);
        $last_day = Carbon::parse($data['date'])->endOfMonth()->day;
        $group_id = $data['group_id'];
        $group    = ProfileGroup::query()->findOrFail($group_id);
        $days     = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];

        $salaries = [];

       // $group_users = $group->users()->pluck('id')->toArray();
        $group_users = json_decode($group->users, true);

        $working = (new UserService)->getEmployees($group_id, $date->format('Y-m-d'));
        $working = collect($working)->pluck('id')->toArray();

        $fired = (new UserService)->getFiredEmployees($group_id, $date->format('Y-m-d'));
        $fired = collect($fired)->pluck('id')->toArray();


        $group_users = array_merge($fired, $working);

            ////
            /**
             *
             *
             *
             *
             *
             */
        $arr = Salary::salariesTable(
            3, // user_type
            $date->format('Y-m-d'),
            $group_users,
            $group_id
        );

      //  if(auth()->id() == 5) dd(collect($arr['users'])->pluck('id', 'full_name'));
        foreach ($days as $day) {

            $salaries[$day] = 0;

            foreach ($arr['users'] as $user) {
                if(isset($user['test_bonus'][$day])) $salaries[$day] += (float) $user['test_bonus'][$day];
                if(isset($user['bonuses'][$day]))    $salaries[$day] += (float) $user['bonuses'][$day];
                if(isset($user['awards'][$day]))     $salaries[$day] += (float) $user['awards'][$day];
                if(isset($user['earnings'][$day]))   $salaries[$day] += (float) $user['earnings'][$day];

                if(isset($user['fine'][$day])) {


                    foreach($user['fine'][$day] as $fines) {
                        foreach($fines as $fine) {
                            $salaries[$day] -= (float) $fine;
                        }


                    }
                }

                if($day == 1) $salaries[$day] += (float) $user['kpi'];
            }

        }

        return $salaries;
    }

    /**
     * salaries Table
     */
    public static function salariesTable($user_types, $date, $users_ids, $group_id = 0)
    {
        $date = Carbon::parse($date)->day(1);

        $group = ProfileGroup::find($group_id);

        $users = User::withTrashed();

        $users->whereIn('users.id', array_unique($users_ids));

        $users->with([
            'user_description',
            'salaries' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year);
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
                $q->selectRaw("*,DATE_FORMAT(day, '%e') as date")
                    ->whereMonth('day', $date->month)
                    ->whereYear('day', $date->year)
                    ->where('status', 1);
            },
            'trackHistory' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->whereMonth('date', '=', $date->month)
                    ->whereYear('date', $date->year);
            },
            'obtainedBonuses' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->whereMonth('date', '=', $date->month)
                    ->whereYear('date', $date->year);
            },
            'testBonuses' => function ($q) use ($date) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")
                    ->whereMonth('date', '=', $date->month)
                    ->whereYear('date', $date->year);
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
        ]);

        $users = $users->get([
            'users.id',
            'users.email',
            'users.deleted_at',
            'users.name',
            'users.last_name',
            'user_type',
            'users.created_at',
            'full_time',
            'users.working_day_id',
            'users.working_time_id',
            'users.work_chart_id',
            'users.first_work_day',
            'users.timezone',
        ]);

        $data['users'] = [];
        $data['total_resources'] = 0;

        foreach ($users as $key => $user) {
            /**
             * if internship is paid
             */
            $internshipPayRate = $user->internshipPayRate();

            /**
             * count fines
             */
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

            /**
             * Вычисление даты принятия
             */
            $user_applied_at = $user->applied_at();

            /////// TTS
            $tts = $user->timetracking
                    ->where('time', '>=', Carbon::parse($user_applied_at)->timestamp);

            $trainee_days     = $user->daytypes->whereIn('type', [5,7]);
            $retraining_days  = $user->daytypes->whereIn('type', [6]);
            $absent_days      = $user->daytypes->whereIn('type', [2]);
            $tts_before_apply = $user->timetracking
                ->where('time', '<', Carbon::parse($user_applied_at)->timestamp);


            $earnings    = [];
            $hourly_pays = [];
            $hours       = [];
            $trainings   = [];

            /**
             * worktime hours in day
             */
            if($user->working_time_id == 1) {
                $worktime = 8;
            } else {
                $worktime = 9;
            }

            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $statTotalHour = null;
                if ($group->time_address != 0) {
                    $dayInMonth = Carbon::create($date->year, $date->month, $i);
                    $userStat = UserStat::getTimeTrackingActivity($user, $dayInMonth, $group->time_address);
                    if ($userStat) $statTotalHour = floatval($userStat->value);
                }

                $d = '' . $i;

                if(strlen ($i) == 1) $d = '0' . $i;

                //count hourly pay
                $s = $user->salaries->where('day', $d)->first();

                $zarplata = $s ? $s->amount : 70000;
                $schedule = $user->schedule(true);
                $workChart = $user->workChart;

                // Проверяем установлена ли время отдыха
                if ($workChart && $workChart->rest_time != null){
                    $lunchTime = $workChart->rest_time;
                    $hour = floatval($lunchTime / 60);
                    $userWorkHours = max($schedule['end']->diffInSeconds($schedule['start']), 0);
                    $working_hours = round($userWorkHours / 3600, 1) - $hour;
                }else{
                    $lunchTime = 1;
                    $userWorkHours = max($schedule['end']->diffInSeconds($schedule['start']), 0);
                    $working_hours = round($userWorkHours / 3600, 1) - $lunchTime;
                }

                // Проверяем тип рабочего графика, так как есть у нас недельный и сменный тип
                $workChartType = $workChart->work_charts_type ?? 0;
                if ($workChartType === 0 || $workChartType === WorkChartModel::WORK_CHART_TYPE_USUAL){
                    $ignore = $user->getCountWorkDays();   // Какие дни не учитывать в месяце
                    $workdays = workdays($date->year, $date->month, $ignore);
                }
                elseif ($workChartType === WorkChartModel::WORK_CHART_TYPE_REPLACEABLE) {
                    $workdays = $user->getCountWorkDaysMonth();
                }
                else {
                    throw new Exception(message: 'Проверьте график работы', code: 400);
                }

                $hourly_pay = $zarplata / $workdays / $working_hours;
                $hourly_pays[$i] = round($hourly_pay, 2);

                // add to array

                $earnings[$i]    = null;
                $hourly_pays[$i] = null;
                $hours[$i]       = null;
                $trainings[$i]   = null;

                $x = $tts->where('day', $i);
                $y = $tts_before_apply->where('day', $i);
                $t = $trainee_days->where('day', $i)->first();
                $r = $retraining_days->where('day', $i)->first();
                $a = $absent_days->where('day', $i)->first();

                if (empty($statTotalHour)) {
                    if ($a) {
                        $earnings[$i] = 0;
                        $hours[$i] = 0;
                    } else if ($r) { // переобучение
                        $trainings[$i] = true;
                        $total_hours = 0;

                        if ($x->count() > 0) {
                            $total_hours = $x->sum('total_hours');
                        }

                        $earning = $total_hours / 60 * $hourly_pay * 0.5;
                        $earnings[$i] = round($earning); // стажировочные на пол суммы

                        $hours[$i] = round($total_hours / 60, 1);

                    } else if ($t) { // день отмечен как стажировка
                        $trainings[$i] = true;

                        $earning = $hourly_pay * $worktime * $internshipPayRate;
                        $earnings[$i] = round($earning); // стажировочные на пол суммы

                        $hours[$i] = round($worktime / 2, 1);
                    } else if ($x->count() > 0) { // отработанное время есть
                        $total_hours = $x->sum('total_hours');
                        $earning = $total_hours / 60 * $hourly_pay;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($total_hours / 60, 1);

                    } else if ($y->count() > 0) { // отработанное врея есть до принятия на работу
                        $total_hours = $y->sum('total_hours');
                        $earning = $total_hours / 60 * $hourly_pay;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($total_hours / 60, 1);
                    }
                } else {
                    if ($a) {
                        $earnings[$i] = 0;
                        $hours[$i] = 0;
                    } else if ($r) { // переобучение
                        $trainings[$i] = true;

                        $earning = $statTotalHour * $hourly_pay * 0.5;
                        $earnings[$i] = round($earning); // стажировочные на пол суммы

                        $hours[$i] = round($statTotalHour, 1);

                    } else if ($t) { // день отмечен как стажировка
                        $trainings[$i] = true;

                        $earning = $hourly_pay * $worktime * $internshipPayRate;
                        $earnings[$i] = round($earning); // стажировочные на пол суммы

                        $hours[$i] = round($worktime / 2, 1);
                    } else if ($x->count() > 0) { // отработанное врея есть
                        $earning = $statTotalHour * $hourly_pay;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($statTotalHour, 2);

                    } else if ($y->count() > 0) { // отработанное врея есть до принятия на работу
                        $earning = $statTotalHour * $hourly_pay;
                        $earnings[$i] = round($earning);
                        $hours[$i] = round($statTotalHour, 1);
                    }
                }
            }

            /**
             * Subtract from salary headphone price
             */
            $headphones_amount = 0;

            if($user->user_description) {
                $headphones_date = Carbon::parse($user->user_description->headphones_date);
                if($user->user_description->headphones_amount > 0
                    && $headphones_date->year == $date->year
                    && $headphones_date->month == $date->month) {
                    $headphones_amount = $user->user_description->headphones_amount;
                }
            }

            /**
             * Advanсes
             */
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

            /**
             * Bonus added manually from salary page
             */
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

            /**
             * Bonus from settings
             */
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

            /**
             * Bonus for course tests
             */
            $test_bonus = [];

            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $d = '' . $i;
                if(strlen ($i) == 1) $d = '0' . $i;

                $x = $user->testBonuses->where('day', $d)->sum('amount');
                if($x > 0) {
                    $test_bonus[$i] = $x;
                } else {
                    $test_bonus[$i] = null;
                }
            }

            /**
             * add to user new fields
             */
            $user->worked_days = Carbon::parse($user_applied_at)->timestamp;
            $user->fines_total = $fines_total;
            $user->trainings   = $trainings;
            $user->hourly_pays = $hourly_pays;
            $user->hours       = $hours;
            $user->fine        = $fines;
            $user->avanses     = $avanses;
            $user->earnings    = $earnings;
            $user->bonuses     = $bonuses;
            $user->test_bonus  = $test_bonus;
            $user->awards      = $awards;

            /**
             * Данные для колонки Налоги.
             */
            $user->taxes  = $user->taxes()
                ->select('taxes.id', 'taxes.name',
                    DB::raw('CASE WHEN user_tax.value > 0 THEN user_tax.value ELSE taxes.value END AS value'),
                    'taxes.is_percent')
                ->wherePivot('created_at', '<=', $date->lastOfMonth()->format('Y-m-d'))
                ->get()
                ->map(function($tax) use ($user)
            {
                $salary = $user->zarplata?->zarplata;
                $tax->amount = $tax->is_percent ? $salary * ($tax->value / 100) : $tax->value;

                return $tax;
            });


            /**
             * If user has edited Salary take it
             */
            $user->edited_salary = null;

            $editedSalary = EditedSalary::where('user_id', $user->id)
                                    ->whereYear('date', $date->year)
                                    ->whereMonth('date', $date->month)
                                    ->first();

            if($editedSalary) {
                $ku = User::withTrashed()->find($editedSalary->author_id);
                $editedSalary->user = $ku ? $ku->last_name . ' ' . $ku->name : 'Неизвестно';
                $user->edited_salary = $editedSalary;
            }

            /**
             * If user has edited KPI take it
             */
            $editedKpi = EditedKpi::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            $user->edited_kpi = null;
            if($editedKpi) {
                $user->kpi = $editedKpi->amount;

                $ku = User::withTrashed()->find($editedKpi->author_id);
                $editedKpi->user = $ku ? $ku->last_name . ' ' . $ku->name : 'Неизвестно';

                $user->edited_kpi = $editedKpi;
            } else {
                $user->kpi = Kpi::userKpi($user->id, $date);
            }

            /**
             * If user has edited Bonus for month take it
             */
            $editedBonus = EditedBonus::where('user_id', $user->id)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->first();

            $user->edited_bonus = null;
            if($editedBonus) {
                $ku = User::withTrashed()->find($editedBonus->author_id);
                $editedBonus->user = $ku ? $ku->last_name . ' ' . $ku->name : 'Неизвестно';

                $user->edited_bonus = $editedBonus;
            }

            // add to array
            $data['users'][] = $user;
            $data['total_resources'] += $user->full_time == 1 ? 1 : 0.5;
        }

        /**
         * sort users by worked days from applied date
         */
        $worked_days = array_column($data['users'], 'worked_days');
        array_multisort($worked_days, SORT_DESC, $data['users']);

        /**
         * token
         */
        $data['auth_token'] = Auth::user()->remember_token;

        return $data;
    }

    public static function getAllTotals($date, $groups, $user_types = self::ALL_USERS){

        $month_start = Carbon::parse($date)->startOfMonth();

        $month = Carbon::parse($date)->startOfMonth();

        $all_total = [];

        $okpi = 0;
        $osal = 0;
        $obon = 0;

        foreach ($groups as $group){
            $internshipPayRate = $group->paid_internship == 1 ? 0.5 : 0;
            $all_total[$group->id] = 0;

            $user_ids = [];
            if($user_types == self::ALL_USERS) {
                $working = (new UserService)->getEmployeesAll($group->id, $date);
                $fired = (new UserService)->getFiredEmployeesAll($group->id, $date);

                $user_ids = array_merge(
                    collect($working)->pluck('id')->toArray(),
                    collect($fired)->pluck('id')->toArray()
                );
            } else if($user_types == self::WORKING_USERS) {
                $working = (new UserService)->getEmployeesAll($group->id, $date);
                $user_ids = collect($working)->pluck('id')->toArray();
            } else if($user_types == self::FIRED_USERS) {
                $fired = (new UserService)->getFiredEmployeesAll($group->id, $date);
                $user_ids = collect($fired)->pluck('id')->toArray();
            }
            $users = self::getUsersDataV2($month_start, $user_ids);

            foreach ($users as $user){
                $debug = [];
                if($user->user_description && $user->user_description->is_trainee == 0) {}
                else {
                    // dump('skip_no_desc');
                    continue;
                }

                if(count($user->groups) > 0 && $user->groups[0]->id != $group->id) {
                    // dump('skip_no_group');
                    continue;
                }

                $hourly_pay = $user->hourly_pay($month->format('Y-m-d'));
                // dump('hourly_pay '.$hourly_pay);

                $user_applied_at = null;
                $ud = $user->user_description;
                if($ud && $ud->applied) {
                    $user_applied_at = $ud->applied;
                }
                if($user_applied_at == null) {
                    $user_applied_at = $user->created_at;
                }

                $tts = $user->timetracking->where('time', '>=', Carbon::parse($user_applied_at)->timestamp);
                $trainee_days = $user->daytypes->whereIn('type', [5,6,7]);
                $tts_before_apply = $user->timetracking->where('time', '<', Carbon::parse($user_applied_at)->timestamp);

                $earnings = [];
                $hourly_pays = [];
                $hours = [];

                $trainings = [];

                if($user->working_time_id == 1) {
                    $worktime = 8;
                } else {
                    $worktime = 9;
                }

                $schedule = $user->scheduleFast();
                $lunchTime = 1;
                $working_hours = max($schedule['end']->diffInHours($schedule['start']) - $lunchTime, 0);

                $ignore = $user->working_day_id == 1 ? [6,0] : [0];
                $workdays = workdays($month->year, $month->month, $ignore);

                for ($i = 1; $i <= $month->daysInMonth; $i++) {
                    $d = '' . $i;
                    if(strlen($i) == 1) $d = '0' . $i;

                    //count hourly pay
                    $s = $user->salaries->where('day', $d)->first();
                    $zarplata = $s ? $s->amount : 70000;
                    $hourly_pay = $zarplata / $workdays / $working_hours;

                    $hourly_pays[$i] = round($hourly_pay, 2);

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
                    } else if($y->count() > 0) { // отработанное врея есть до принятия на работу
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

                $awards = 0;
                if($user->kpi_obtained_bonuses->count()){
                    $awards = $user->kpi_obtained_bonuses->sum('amount');
                }


                $kpi = 0;
                if ($user->edited_kpi->count()) {
                    $kpi = $user->edited_kpi->sum('amount');
                } else if ($user->saved_kpi->count()) {
                    $kpi = $user->saved_kpi->sum('total');
                }

                $user_total = 0;
                $total_bonuses = (float)$awards;
                $total_salary = 0;


                //   if($user->id == 18392) dd($earnings);

                for($i=1;$i<=$month->daysInMonth;$i++) {
                    $total_bonuses += (float)$bonuses[$i];
                    $total_salary += (float)$earnings[$i];
                }

                $total_bonuses += (float)$user->testBonuses->sum('amount');

                $user_total += $total_salary;

                if($user->edited_bonuses->count()) {
                    $amount = $user->edited_bonuses->sum('amount');
                    $user_total += (float)$amount;
                    $total_bonuses = (float)$amount;
                } else {
                    $user_total += $total_bonuses;
                }

                $avans = $user->salaries->sum('paid');

                $fines = $user->fines->sum('penalty_amount');

                $user_total += (float)$kpi;

                $total_must_count = (float)$user_total - $avans - $fines;

                if($total_must_count > 0)  {
                    $all_total[$group->id] += $user_total;

                    $okpi += $kpi;
                    $obon += $total_bonuses;
                    $osal += $total_salary;
                }
            }
        }

        return $all_total;
    }
}
