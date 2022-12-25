<?php

namespace App\Http\Controllers\Admin;

use App\KpiChange;
use App\Models\Analytics\Activity;
use App\Models\Analytics\ActivityPlan;
use App\Models\Analytics\AnalyticStat;
use App\User;
use App\ProfileGroup;
use App\Kpi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Analytics\KpiIndicator;
use App\Models\Analytics\IndividualKpiIndicator;
use App\Models\Analytics\IndividualKpi;
use App\Models\Analytics\UserStat;
use Illuminate\Support\Facades\View;

class KpiController extends Controller
{   
    public function __construct()
    {
        // View::share('title', 'Админ панель');
        // View::share('menu', 'timetracking');
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        View::share('title', 'KPI');
        View::share('menu', 'timetracking');

        return view('kpi')->with([
            'page' => 'kpi'
        ]);
    }

    public function saveKPI(Request $request)
    {
        $user = User::bitrixUser();
        $user_id = ['user_id' => $user->id];
        $inputs = $request->only([
            'group_id',
            'kpi_80_99',
            'kpi_100',
            'nijn_porok',
            'verh_porok',
        ]); 
        
        // @TODO SAve KPI to activity tables ud_ves and activity_name

        $activities = Activity::withTrashed()->where('group_id', $request->group_id)->get();
        
        if($request->kpi_indicators) {
            foreach($request->kpi_indicators as $kpi_indicator) {
                $kpi_ind = KpiIndicator::find($kpi_indicator['id']);

                if($kpi_ind) {
                    $kpi_ind->name = $kpi_indicator['name'];
                    $kpi_ind->ud_ves = $kpi_indicator['ud_ves'];
                    $kpi_ind->plan_unit = $kpi_indicator['plan_unit'];
                    $kpi_ind->unit = $kpi_indicator['unit'] ?? '';
                    $kpi_ind->plan = $kpi_indicator['daily_plan'];
                    $kpi_ind->activity_id = $kpi_indicator['activity_id'];
                    $kpi_ind->save();

                    if($kpi_indicator['activity_id'] != 0) {
                        $act = Activity::withTrashed()->find($kpi_indicator['activity_id']);

                        $act->ud_ves = $kpi_indicator['ud_ves'];
                        $act->plan_unit = $kpi_indicator['plan_unit'];
                        $act->unit = $kpi_indicator['unit'] ?? '';
                        $act->daily_plan = $kpi_indicator['daily_plan'];
                        $act->save();
    
                        $activity_plan = ActivityPlan::where([
                                'activity_id' => $act->id,
                                'month' => date('m'),
                                'year' => date('Y'),
                            ])->first();
    
                        if($activity_plan) {
                            $activity_plan->update([
                                    'ud_ves' => $kpi_indicator['ud_ves'],
                                    'plan_unit' => $kpi_indicator['plan_unit'],
                                    'unit' => $kpi_indicator['unit'] ?? '',
                                    'plan' => $kpi_indicator['daily_plan'],
                                    'month' => date('m'),
                                    'year' => date('Y'),
                                ]);
                        } else {
                            ActivityPlan::create([
                                    'activity_id' => $act->id,
                                    'ud_ves' => $kpi_indicator['ud_ves'],
                                    'plan_unit' => $kpi_indicator['plan_unit'],
                                    'unit' => $kpi_indicator['unit'] ?? '',
                                    'plan' => $kpi_indicator['daily_plan'],
                                    'month' => date('m'),
                                    'year' => date('Y'),
                                ]);
                        }
                        
                    }

                    if($kpi_indicator['deleted']) {
                        $kpi_ind->delete();
                    }
                    
                } else {
                    $kpi_ind = KpiIndicator::create([
                        'name' => $kpi_indicator['name'],
                        'ud_ves' => $kpi_indicator['ud_ves'],
                        'plan_unit' => $kpi_indicator['plan_unit'],
                        'unit' => $kpi_indicator['unit'] ?? '',
                        'group_id' => $request->group_id,
                        'plan' => $kpi_indicator['daily_plan'],
                        'activity_id' => $kpi_indicator['activity_id'],
                    ]);
                }
                    
                
            }
        }

        $inputs = array_merge($inputs, $user_id);

        $kpi = Kpi::where('group_id', $request->group_id)->first();
        
        if (is_null($kpi)) {
            $kpi = Kpi::create($inputs);
            
            KpiChange::create([
                'kpi_id' => $kpi->id,
                'group_id' => $request->group_id,
                'kpi_80_99' => $request->kpi_80_99,
                'kpi_100' => $request->kpi_100,
                'nijn_porok' => $request->nijn_porok,
                'verh_porok' => $request->verh_porok,
                'user_id' => $user->id,
                'month' => date('m'),
                'year' => date('Y'),
            ]);

        } else {
            $kpi->update($inputs);
            
            $kpi_this_month = KpiChange::where([
                    'kpi_id' => $kpi->id,
                    'month' => date('m'),
                    'year' => date('Y'),
                ])->first();

            $inputs_2 = [
                'kpi_id' => $kpi->id,
                'group_id' => $request->group_id,
                'kpi_80_99' => $request->kpi_80_99,
                'kpi_100' => $request->kpi_100,
                'nijn_porok' => $request->nijn_porok,
                'verh_porok' => $request->verh_porok,
                'user_id' => $user->id,
                'month' => date('m'),
                'year' => date('Y'),
            ];
            
            if($kpi_this_month) {
                $kpi_this_month->update($inputs_2);
            } else {
                KpiChange::create($inputs_2);
            }
        }

    }

    public function saveKpiIndividual(Request $request) {
        
        $inputs = $request->only([
            'group_id',
            'kpi_80_99',
            'kpi_100',
            'nijn_porok',
            'verh_porok',
        ]); 
        
        $inputs = array_merge($inputs, ['user_id' => $request->user_id]);
   
        $kpi = IndividualKpi::where('user_id', $request->user_id)->first();
        
        if(count($request->kpi_indicators) > 0) {

            // Ind kpi
            if (is_null($kpi)) {
                $kpi = IndividualKpi::create($inputs);
            } else {
                $kpi->update([
                    'kpi_80_99' => $request['kpi_80_99'],
                    'kpi_100' => $request['kpi_100'],
                    'nijn_porok' => $request['nijn_porok'],
                    'verh_porok' => $request['verh_porok'],
                ]);
            }

            // ind koi indicator
            foreach($request->kpi_indicators as $kpi_indicator) {
                //dump($kpi_indicator);
                $kpi_ind = IndividualKpiIndicator::find($kpi_indicator['id']);
                
                $activity = Activity::withTrashed()->find($kpi_indicator['activity_id']);

                if($activity) {
                    $plan_unit = $activity->plan_unit;
                    $activity->daily_plan = $kpi_indicator['daily_plan'];
                    $activity->save();
                } else {
                    $plan_unit = 'minutes';
                }

                if($kpi_ind) {
                    $kpi_ind->name = $kpi_indicator['name'];
                    $kpi_ind->ud_ves = $kpi_indicator['ud_ves'];
                    $kpi_ind->source = $kpi_indicator['activity_id'] == -1 ? 'analytic_stat' : 'activity';
                    $kpi_ind->cell = $kpi_indicator['cell'];
                    $kpi_ind->plan_unit = $kpi_indicator['plan_unit'];
                    $kpi_ind->group_id = $kpi_indicator['group_id'];
                    $kpi_ind->unit = $kpi_indicator['unit'] ?? '';
                    $kpi_ind->plan = $kpi_indicator['daily_plan'];
                    $kpi_ind->activity_id = $kpi_indicator['activity_id'];
                    $kpi_ind->save();
    
                    if($kpi_indicator['deleted']) {
                        $kpi_ind->delete();
                    }
                    
                } else {
                    $kpi_ind = IndividualKpiIndicator::create([
                        'name' => $kpi_indicator['name'],
                        'ud_ves' => $kpi_indicator['ud_ves'],
                        'source' => $kpi_indicator['activity_id'] == -1 ? 'analytic_stat' : 'activity',
                        'cell' => array_key_exists('cell', $kpi_indicator) ? $kpi_indicator['cell'] : null,
                        'plan_unit' => $kpi_indicator['plan_unit'],
                        'unit' => $kpi_indicator['unit'] ?? '',
                        'group_id' => $kpi_indicator['group_id'] ?? 0,
                        'user_id' => $request->user_id,
                        'plan' => $kpi_indicator['daily_plan'],
                        'activity_id' => $kpi_indicator['activity_id'],
                    ]);
                }
                    
                
            }

        } else {
            $kpi_inds = IndividualKpiIndicator::where('user_id', $request->user_id)->delete();
            if($kpi) $kpi->delete(); // Ind kpi
        }

        
    }

    public function getKPI(Request $request)
    {
        $user = User::bitrixUser();
        
        // GET KPI
        $issetKpi =  Kpi::where('group_id', $request->group_id)->first();

        if($issetKpi) {
            $kpi = $issetKpi;
        } else {
            $kpi = Kpi::create([
                'group_id' => $request->group_id,
                'kpi_80_99' => 0,
                'kpi_100' => 0,
                'nijn_porok' => 80,
                'verh_porok' => 100,
                'user_id' => $user->id
            ]);
        }

        $time_rate = $user->full_time == 1 ? 1: 0.5;

        $kpi->kpi_80_99 = $kpi->kpi_80_99 * $time_rate;
        $kpi->kpi_100 = $kpi->kpi_100 * $time_rate;

        $group = ProfileGroup::find($request->group_id);
        $activities = Activity::withTrashed()->where('group_id', $request->group_id)->get(['name', 'id'])->toArray();

        $kpi_indicators = KpiIndicator::where('group_id', $request->group_id)->get();

        $applied_from = $user->workdays_from_applied(date('Y-m-d'), $group->workdays);
        
        $ignore = $group->workdays == 5 ? [0,6] : [0];
        $workdays = workdays(date('Y'), date('m'), $ignore);
        
        if($request->group_id == 53 && date('Y') == 2022 && date('m') == 3) {
            $workdays = 19;
        } else if($request->group_id == 57  && date('Y') == 2022 && date('m') == 3) {
            $workdays = 22;
        } else {
            $workdays = workdays(date('Y'), date('m'), $ignore);
        }
        
        
        foreach ($kpi_indicators as $kpi_indicator) {

            $kpi_indicator->workdays = $workdays;
            if($applied_from != 0) {
                $kpi_indicator->workdays = $applied_from;
            }

            if($kpi_indicator->activity_id != 0) {
                $activity = Activity::withTrashed()->find($kpi_indicator->activity_id);
                
                $ignore = [0,6,5,4,3,2,1];
                for($i=0;$i<$activity->weekdays;$i++) array_pop($ignore);  // Какие дни не учитывать в месяце
                $kpi_indicator->workdays = workdays(date('Y'), date('m'), $ignore);
                $workdays = workdays(date('Y'), date('m'), $ignore);
            }

            $kpi_indicator->groups = [];
            if($request->is_admin) {
                $kpi_indicator->completed = 100;
                $kpi_indicator->completed_value = 0;
            } else {
                if($request->activeuserid && $kpi_indicator->activity_id != 0) {
                    $activity = Activity::withTrashed()->find($kpi_indicator->activity_id);
                    
                    $ignore = [0,6,5,4,3,2,1];
                    for($i=0;$i<$activity->weekdays;$i++) array_pop($ignore);  // Какие дни не учитывать в месяце
                    $workdays = workdays(date('Y'), date('m'), $ignore);
                    $kpi_indicator->workdays = workdays(date('Y'), date('m'), $ignore);
               
                    $completed = UserStat::getActivityProgress($request->activeuserid, $request->group_id, $activity, '', true);
                    
                    if($completed['percent'] > 100) { // перевыполнение не учитывается
                        $completed['percent'] = 100;
                    }

                    $kpi_indicator->completed = $completed['percent'];
                    $kpi_indicator->completed_value = $completed['value'];
                } else {
                    $kpi_indicator->completed = 0;
                    $kpi_indicator->completed_value = 0;
                }
            }
            
            $kpi_indicator->sum_prem = 0;
            $kpi_indicator->result = 0;

            $kpi_indicator->daily_plan = $kpi_indicator->plan;
            
            if($user->full_time == 0 && $kpi_indicator->plan_unit == 'minutes' && !$request->is_admin) {
                $kpi_indicator->daily_plan = $kpi_indicator->daily_plan / 2;
            }
            
            $kpi_indicator->checked = false;
            $kpi_indicator->deleted = false;
     
        }

        return response()->json([
            'kpi' => $kpi,
            'activities' => $activities,
            'kpi_indicators' => $kpi_indicators,
            'workdays' => $applied_from == 0 ? $workdays : $applied_from,
        ]);
    }

    private function getWeekdays() {
        $day->isWeekday();
    }

    /**
     * Individual kpi in profile edit page
     */
    public function getKpiIndividual(Request $request)
    {
        if($request->activeuserid != 0 ) {
            $user = User::withTrashed()->find($request->activeuserid);
        } else {
            $user = User::withTrashed()->find($request->group_id);
        }
        
        $kpi =  IndividualKpi::where('user_id', $user->id)->first();

        $time_rate = $user->full_time == 1 ? 1: 0.5;

        if($kpi) {
            $kpi->kpi_80_99 = $kpi->kpi_80_99 * $time_rate;
            $kpi->kpi_100 = $kpi->kpi_100 * $time_rate;
        }
        

        // GET ACTIVITY
 
        $activities = [];
        $_groups = Activity::withTrashed()->orderBy('group_id', 'asc')->get()->groupBy('group_id');
        
        foreach ($_groups as $group_id => $_activities) {
            $gr = [];

            $group = ProfileGroup::where('active', 1)->find($group_id);
            
            foreach ($_activities as $activity) {
               

                if($group) {
                    array_push($gr, [
                        'name' => $activity->name,
                        'id' => $activity->id
                    ]);
                }  
            }   

            $activities[$group_id] = $gr;
        }
 
        // $_sort = array_column($activities, 'name');
        // array_multisort($_sort, SORT_ASC, $activities); 


        $kpi_indicators = IndividualKpiIndicator::where('user_id', $user->id)->get();

        //$applied_from = $user->workdays_from_applied(date('Y-m-d'), $group->workdays);
        $applied_from = $user->workdays_from_applied(date('Y-m-d'), 6);

        // $ignore = $group->workdays == 5 ? [0,6] : [0];
        // $workdays = workdays(date('Y'), date('m'), $ignore);
        $workdays = workdays(date('Y'), date('m'), [0]);
        
        $groups = ProfileGroup::where('active', '1')->get();
    
        foreach ($kpi_indicators as $kpi_indicator) {

            $kpi_indicator->workdays = $workdays;
            $kpi_indicator->groups = $groups;

            if($request->is_admin) {
                $kpi_indicator->completed = 100;
                $kpi_indicator->completed_value = 0;
                
                if($kpi_indicator->activity_id != 0) {
                    $activity = Activity::withTrashed()->find($kpi_indicator->activity_id);
                    if($activity) {

                        // count workdays 
                        $ignore = [0,6,5,4,3,2,1];
                        for($i=0;$i<$activity->weekdays;$i++) array_pop($ignore);  // Какие дни не учитывать в месяце
                        $workdays = workdays(date('Y'), date('m'), $ignore);

                        $kpi_indicator->workdays = $workdays;

                        // actions
                       // $kpi_indicator->plan_unit = $activity->plan_unit;

                        if($activity->type == 'turnover' || $activity->type == 'staff') {
                            $kpi_indicator->plan_unit = 'percent';
                        }
                    }
                }
            } else {
                if($request->activeuserid && $kpi_indicator->activity_id != 0 && $kpi_indicator->activity_id != -1) {
                    $activity = Activity::withTrashed()->find($kpi_indicator->activity_id);
                    
            
                    // count workdays 
                    $ignore = [0,6,5,4,3,2,1];
                    for($i=0;$i<$activity->weekdays;$i++) array_pop($ignore);  // Какие дни не учитывать в месяце
                    $workdays = workdays(date('Y'), date('m'), $ignore);
                    

                    $kpi_indicator->workdays = $workdays;
                    if($applied_from != 0) {
                        $kpi_indicator->workdays = $applied_from;
                    }
                    
                  
                    if($activity->group_id == 0) {
                        $completed = 0;
                        $kpi_indicator->completed = $completed;
                        $kpi_indicator->completed_value = $completed;
                        //$kpi_indicator->plan_unit = $activity->plan_unit;
                    } else if($activity->type == 'rentab') { // Impl
                        
                        $date = Carbon::now()->startOfMonth()->format('Y-m-d');

                        // $im = new Impl($kpi_indicator->group_id);     
                        // $impl = $im->setDate($date)->get();

                        $rent = AnalyticStat::getRentability($activity->group_id, $date);

                        $kpi_indicator->completed = round($activity->daily_plan > 0 ? $rent / $activity->daily_plan * 100 : 0, 2);
                        $kpi_indicator->completed_value = round($rent, 2);

                        $kpi_indicator->plan_unit = 'percent';
                    } else if($activity->type == 'staff') {

                        $date = Carbon::now()->startOfMonth()->format('Y-m-d');
                     
                        $kpi_indicator->completed_value = 0;

                        $completed = 0;
                        if((float)$activity->daily_plan > 0) {
                            $completed = $kpi_indicator->completed_value / (float)$activity->daily_plan * 100;
                            $kpi_indicator->completed = round($completed, 1);
                        } 
                        $kpi_indicator->plan_unit = 'percent';
                        //PGU::where()->get();
                    }
                    else if($activity->type == 'turnover') {
                       
                     
                        $date = Carbon::now()->startOfMonth()->format('Y-m-d');
                        $turnover = \App\Classes\Analytics\Recruiting::staff_on_group($date, $activity->group_id);
                    
                        $kpi_indicator->completed_value = $turnover;
                        if($turnover > 0) {
                   
                            $kpi_indicator->completed =  round(((float)$activity->daily_plan) / $turnover * 100, 2);
                        } else {
                            $kpi_indicator->completed = 0;
                        }

                    } else {

                        if($activity->group_id == 48) {

                            if($activity->type == 'conversion') { // Conversion in TableSummaryRecruiting
                                $as = \App\AnalyticsSettings::where('group_id', $kpi_indicator->group_id)->where('date', $date)->where('type', 'basic')->first();
                                $completed =[];
                                $completed['percent'] = 0;
                                $completed['value'] = 0;
                               
                                if($as && array_key_exists(9, $as->data) &&  array_key_exists(0, $as->data) && array_key_exists('fact', $as->data[0]) && array_key_exists('fact', $as->data[9])) {

                                    $res = 0;
                                    if($as->data[0]['fact'] > 0) $res = round($as->data[9]['fact'] / $as->data[0]['fact'] * 100, 2);
                                    $completed['percent'] = $res;
                                    $completed['value'] =  $res;
                                }
                            } else {
                                $completed = UserStat::getTotalActivityProgress($request->activeuserid, $activity, '', true);
                            }

                            
                       
                        } else {
                            $completed = UserStat::getTotalActivityProgress($request->activeuserid, $activity, '', true);
                        }
                        $kpi_indicator->completed = $completed['percent'];
                        $kpi_indicator->completed_value = $completed['value'];
                        $kpi_indicator->plan_unit = $activity->plan_unit;
                    }

                   
                } else if($request->activeuserid &&  $kpi_indicator->source == 'analytic_stat') {

                    $date = Carbon::now()->day(1)->format('Y-m-d');
                    
                    $val = 0;
                    if($kpi_indicator->cell != null) {
                        $val = AnalyticStat::getCellValue($kpi_indicator->group_id, $kpi_indicator->cell, $date);
                    }
                    
                    $kpi_indicator->completed_value = $val;

                    $completed = 0;

                    if($val != 0) {
                        $completed = round($kpi_indicator->plan / $val * 100, 1);
                    }
                    
                    if($kpi_indicator->plan_unit == 'more_sum') {
                        if($val >= $kpi_indicator->plan) {
                            $completed = 100;
                        } else {
                            $completed = 0;
                        }
                    }

                    $kpi_indicator->completed = $completed;
                    
                } else {
                    $kpi_indicator->completed = 0;
                    $kpi_indicator->completed_value = 0;
                }
            }


        
            $kpi_indicator->sum_prem = 0;
            $kpi_indicator->result = 0;

            $kpi_indicator->daily_plan = $kpi_indicator->plan;
            
            if($user->full_time == 0 && $kpi_indicator->plan_unit == 'minutes') {
                $kpi_indicator->daily_plan = $kpi_indicator->daily_plan / 2;
            } 

            if($kpi_indicator->completed > 100) {
                $kpi_indicator->completed = 100;
            }
            $kpi_indicator->checked = false;
            $kpi_indicator->deleted = false;
     
        }

        // RESPONSE
        return [
            'kpi' => $kpi ? $kpi : [
                'user_id' => $request->group_id,
                'kpi_80_99' => 0,
                'kpi_100' => 0,
                'nijn_porok' => 0,
                'verh_porok' => 0,
            ],
            'activities' => $activities,
            'kpi_indicators' => $kpi_indicators,
            'workdays' => $applied_from == 0 ? $workdays : $applied_from,
            'groups' => $groups,
        ];
    }
}
