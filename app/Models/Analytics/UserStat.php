<?php

namespace App\Models\Analytics;

use App\Models\AnalyticsActivitiesSetting;
use App\Models\WorkChart\WorkChartModel;
use App\Repositories\ActivityRepository;
use App\WorkingDay;
use Illuminate\Database\Eloquent\Model;
use App\Models\Analytics\Activity;
use Carbon\Carbon;
use DB;
use App\User;
use App\UserDescription;
use App\ProfileGroup;
use App\QualityRecordWeeklyStat;
use App\QualityRecordMonthlyStat;
use App\Models\Analytics\IndividualKpiIndicator;
use App\Models\Kpi\Bonus;
use App\Service\Department\UserService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class UserStat extends Model
{
    protected $fillable = [
        'date',
        'user_id',
        'activity_id',
        'value',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * get activities
     */
    public static function activities($group_id, $date) {
        $activities = [];
        $acts = Activity::where('group_id', $group_id)->whereIn('view', [Activity::VIEW_DEFAULT, Activity::VIEW_COLLECTION, Activity::VIEW_QUALITY])->orderBy('order', 'desc')->get();

        $group = ProfileGroup::find($group_id);
        $carbon = Carbon::parse($date);
        foreach($acts as $activity) {
            if($activity) {

                //collection kaspi
                $price = 0;
                if($activity->type == 'collection') {
                    $bonuses = Bonus::where('activity_id', $activity->id)->first();
                    if($bonuses) $price = $bonuses->sum;
                }

                // get workdays in month

                $ignore = [0,6,5,4,3,2,1];
		        for($i=0;$i<$activity->weekdays;$i++) array_pop($ignore);  // Какие дни не учитывать в месяце
                $workdays = workdays($carbon->year, $carbon->month, $ignore);

                $plan = (new ActivityRepository)->getDailyPlan($activity, $carbon->year, $carbon->month) ?? null;

                // create item arr
                $item = [
                    'id' => $activity->id,
                    'name' => $activity->name,
                    'editable' => $activity->editable,
                    'daily_plan' => $plan == null ? $activity->daily_plan : $plan->plan,
                    'order' => $activity->order,
                    'group_id' => $activity->group_id,
                    'plan_unit' => $activity->plan_unit,
                    'workdays' => $workdays,
                    'weekdays' => $activity->weekdays,
                    'type' => $activity->type,
                    'view' => $activity->view,
                    'unit' => $activity->unit == '%' ? '%' : '',
                    'table' => 1,
                    'records' => [],
                    'price' => $price
                ];

                if($activity->type == 'default') {
                    $item['records'] = self::form_table($activity->id, $date, $group_id);
                }

                if($activity->type == 'collection') {
                    $item['records'] = self::form_table($activity->id, $date, $group_id);
                }

                if($activity->type == 'quality') {

                    $working = (new UserService)->getEmployees($group_id, $date);
                    $working = collect($working)->pluck('id')->toArray();

                    $fired = (new UserService)->getFiredEmployees($group_id, $date);
                    $fired = collect($fired)->pluck('id')->toArray();

                    $users_ids = array_unique(array_merge($working, $fired));

                    $hasRecords = QualityRecordWeeklyStat::query()
                        ->where('month', $carbon->month)
                        ->where('year', $carbon->year)
                        ->where('group_id', $group_id)
                        ->select('user_id')
                        ->pluck('user_id')
                        ->toArray();

                    $users_ids = array_unique(array_merge($users_ids, $hasRecords));

                    $item['records'] = QualityRecordWeeklyStat::table($users_ids, $date);
                }

                array_push($activities, $item);
            }
        }

        return $activities;
    }

    /**
     * Form activity table
     */
    public static function form_table($activity_id, $date, $groupId = null) {
        $table = [];

        $activity = Activity::find($activity_id);




        if($activity) {

            $c_date = Carbon::parse($date)->format('Y-m');

            $has_records_on_user_stats =  self::where('date', 'like', $c_date . '%')
                ->where('activity_id', $activity->id)
                ->get(['user_id'])
                ->pluck('user_id')
                ->toArray();
            $date = Carbon::parse($date)->day(1)->format('Y-m-d');


            /**
             * get All Users in group
             */
            $working = ProfileGroup::employees($activity->group_id, $date, 1);
            $fired =  ProfileGroup::employees($activity->group_id, $date, 2);

            $working = (new UserService)->getEmployees($activity->group_id, $date);
            $working = collect($working)->pluck('id')->toArray();

            $fired = (new UserService)->getFiredEmployees($activity->group_id, $date);
            $fired = collect($fired)->pluck('id')->toArray();

            $users_ids = array_unique(array_merge($working, $fired));

            $users = array_merge($users_ids, $has_records_on_user_stats);
            $users = array_unique($users);

            // if (Schema::hasColumn((new AnalyticsActivitiesSetting)->getTable(), AnalyticsActivitiesSetting::COLUMN_PREFIX.$activity_id)){
            //     $removeIds = AnalyticsActivitiesSetting::where('group_id', $groupId)->select(AnalyticsActivitiesSetting::COLUMN_PREFIX.$activity_id)
            //         ->first()->toArray() ?? null;
            //     if ($removeIds){
            //         $removeIdsArray = json_decode($removeIds[AnalyticsActivitiesSetting::COLUMN_PREFIX.$activity_id]);
            //         $users = array_diff($users, $removeIdsArray);
            //     }
            // }

            /**
             * form table row
             */
            foreach($users as $user_id) {
                $item = [];

                $localUser = User::withTrashed()->find($user_id);
                if(!$localUser) continue;

                if($localUser->deleted_at && $localUser->deleted_at != '0000-00-00 00:00:00') {
                    $fired = 1;
                } else {
                    $fired = 0;
                }

                $ud = UserDescription::where('user_id', $localUser->id)->first();
                //$applied_from = $localUser->workdays_from_applied($date, $group_1->workdays);
                $user = User::query()->find($user_id);

                $workDay = WorkingDay::SIX_DAYS;

                if (isset($user->working_day_id))
                {
                    $workDay = $user->working_day_id == 1 ? WorkingDay::FIVE_DAYS : WorkingDay::SIX_DAYS;
                }

                $applied_from = $localUser->workdays_from_applied($date, $workDay);

                $work_days = $user == null ? 26 : WorkChartModel::workdaysPerMonth($user) ?? 26;

                if($applied_from > 0){
                    $work_days = $applied_from;
                }
                $item = [
                    'name' => $localUser->name,
                    'lastname' => $localUser->last_name,
                    'fullname' => trim($localUser->last_name . ' ' . $localUser->name),
                    'email' => $localUser->email,
                    'full_time' => $localUser->full_time,
                    'id' => $localUser->id,
                    'fired' => $fired,
                    'is_trainee' => $ud && $ud->is_trainee == 1,
                    'applied_from' => $applied_from,
                    'group' => '',
                    'plan' => $activity->daily_plan * $work_days,
                ];


                $stats = self::select([
                        DB::raw('DAY(date) as day'),
                        'user_id',
                        'value',
                    ])
                    ->where('activity_id', $activity->id)
                    ->where('date', 'like', $c_date . '%')
                    ->where('user_id', $user_id)
                    ->get();

                    foreach($stats as $stat) {
                        $item[$stat->day] = (float)$stat->value;
                    }

                array_push($table, $item);
            }
        }

        return $table;
    }

    public static function total_for_day($activity_id, $date) {
        if($activity_id == null)  return 0;
        $items = self::where('activity_id', $activity_id)->where('date', $date)->get();

        $act = Activity::find($activity_id);
        if($act && ($act->plan_unit == 'minutes' || $act->plan_unit == 'less_sum')) {
            $method = 'sum';
        }  else {
            $method = 'avg';
        }

        $total = 0;
        $count = 0;
        foreach($items as $item) {
            $total += (float)$item->value;
            if((float)$item->value > 0) $count++;
        }


        if($method == 'avg') {
            if($count > 0) {
                $total = round($total / $count, 1);
            } else {
                $total = 0;
            }
        }
        return $total;
    }

    public static function total_for_month($activity_id, $date, $type = 'sum') {
        if($activity_id == null)  return 0;
        $items = self::where('activity_id', $activity_id)
            ->where('date', 'like', Carbon::parse($date)->format('Y-m') . '%')
            ->get();

        $act = Activity::withTrashed()->find($activity_id);
        if($act && ($act->plan_unit == 'minutes' || $act->plan_unit == 'less_sum')) {
            $method = 'sum';
        }  else {
            $method = 'avg';
        }


        if($act && $act->plan_unit == 'minutes' && $type == 'avg') {
            $method = 'avg';
        }

        $total = 0;
        $count = 0;
        foreach($items as $item) {
            $total += (float)$item->value;
            if((float)$item->value > 0) $count++;
        }


        if($method == 'avg') {
            if($count > 0) {
                $total = round($total / $count, 1);
            } else {
                $total = 0;
            }
        }

        if($act->type == 'staff') {

            $userIds = [];
            $total = UserDescription::where('is_trainee', 0)
                ->whereIn('user_id', $userIds)
                ->get()
                ->count();
        }

        if($act->type == 'turnover') {
            $total = \App\Classes\Analytics\Recruiting::staff_on_group($date, $act->group_id);
        }

        return $total;
    }


    /**
     * Прогресс выполнения активности сотрудником для вычисления KPI
     */
    public static function getActivityProgress(int $user_id, int $group_id, Activity $activity, string $date = '', $return_value_and_percent = false)
    {
        $test_id = 1;
        if($user_id == $test_id)  $date = '2022-07-01';
        if($date == '') {
            $date = Carbon::now()->day(1)->format('Y-m-d');
        }


        $carbon = Carbon::parse($date);
        $user = User::withTrashed()->find($user_id);



        if($group_id == 0) {
            $ignore = [0];
            $applied_from = $user->workdays_from_applied($date, 6);
        } else {
            $g = ProfileGroup::find($group_id);
            $ignore = $g && $g->workdays == 5 ? [0,6] : [0];
            $applied_from = $user->workdays_from_applied($date, $g ? $g->workdays : 6);
        }



        // count workdays for plan
        $ignore = [0,6,5,4,3,2,1];
        for($i=0;$i<$activity->weekdays;$i++) array_pop($ignore);  // Какие дни не учитывать в месяце
        $workdays = workdays($carbon->year, $carbon->month, $ignore);

        // records
        $records = self::where([
            'user_id' => $user_id,
            'activity_id' => $activity->id
        ])
        ->where('date', 'like', Carbon::parse($date)->format('Y-m') . '%')->get();

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

            if($user_id == $test_id) dump($activity->daily_plan);
            if($user_id == $test_id)  dump($workdays);
            if($user_id == $test_id)  dump($total);
            if($user_id == $test_id)  dump($result);

            if($return_value_and_percent) {
                return [
                    'value' => (int)$total,
                    'percent' => number_format($result, 2),
                ];
            } else {

                return $result;
            }
        }

        ///////////////////////////////

        $total = 0;
        if($records->count() > 0) {
            $count = 0;
            foreach($records as $record) {
                $total += (float)$record->value;
                if((float)$record->value > 0) {
                    $count++;
                }
            }

            if((float)$activity->daily_plan != 0) {

                if($activity->plan_unit == 'minutes') {
                    $_plan = (float)$activity->daily_plan;
                    if($user->full_time == 0) {
                        $_plan =  (float)$activity->daily_plan / 2;
                    }

                    if($applied_from != 0) {
                        $result = $total / ($_plan * $applied_from) * 100;
                        // if($user_id == 15551)  dump($total);
                    } else {
                        $result = $total / ($_plan * $workdays) * 100;
                    }


                }

                if($activity->plan_unit == 'less_sum') {
                    if($activity->daily_plan - $total > 0) {
                        $result = 100;
                    } else {
                        $result = 0;
                    }
                   // $result = ($activity->daily_plan - $total) / $activity->daily_plan * 100;
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

                if($activity->plan_unit == 'more_sum') {
                    if($total - $activity->daily_plan >= 0) {
                        $result = 100;
                    } else {
                        $result = 0;
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
        if($user_id == $test_id) dump($activity->daily_plan);
        if($user_id == $test_id)  dump($workdays);
        if($user_id == $test_id)  dump($total);
        if($user_id == $test_id)  dump($result);


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
     * Обший прогресс выполнения активности, для индивидуального KPI
     */
    public static function getTotalActivityProgress(int $user_id, Activity $activity, string $date = '', $return_value_and_percent = false)
    {
        if($date == '') {
            $date = Carbon::now()->day(1)->format('Y-m-d');
        }

        //get users
        $user_ids = self::where([
                'activity_id' => $activity->id
            ])
            ->where('date', 'like', Carbon::parse($date)->format('Y-m') . '%')
            ->get(['user_id'])
            ->pluck('user_id')
            ->toArray();
        //foreach users



        $total = 0;
        $count = 0;

        foreach ($user_ids as $key => $_user_id) {
            $_total = self::getActivityProgress($_user_id, 0, $activity, $date, true);
            //memp($_total);
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

            if($plan_unit == 'more_sum') {
                if($total > (float)$daily_plan) {
                    $result = 100;
                } else {
                    $result = 0;
                }
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
     * Сохранить UserStat по quality
     *
     * date
     * user_id
     * value
     * group_id
     */
    public static function saveQuality(array $data) : void
    {
        $data['activity_id'] = Activity::qualityId($data['group_id']);

        $us = UserStat::where([
            'date'        => $data['date'],
            'user_id'     => $data['user_id'],
            'activity_id' => $data['activity_id'],
        ])->first();

        if($us) {
            $us->value = $data['value'];
            $us->save();
        } else {
            UserStat::create([
                'date'        => $data['date'],
                'user_id'     => $data['user_id'],
                'activity_id' => $data['activity_id'],
                'value'       => $data['value'],
            ]);
        }
    }

    public static function getTimeTrackingActivity($user, $dayInMonth){
        return self::where('user_id', $user->id)
            ->where('date', $dayInMonth->format('Y-m-d'))
            ->where('created_at', '>', $dayInMonth->startOfMonth()->format('Y-m-d H:i:s'))
            ->where('activity_id', Activity::ACTIVITY_UCHET_TIME)
            ->first();
    }
}
