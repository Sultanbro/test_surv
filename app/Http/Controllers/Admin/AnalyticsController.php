<?php

namespace App\Http\Controllers\Admin;

use DB;
use View;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Components\TelegramBot;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IntellectController as IC;
use App\Classes\Helpers\Phone;
use App\External\Bitrix\Bitrix;
use App\Classes\Analytics\Recruiting;
use App\Classes\Analytics\Ozon;
use App\Classes\Analytics\Lerua;
use App\Classes\Analytics\DM;
use App\Classes\Analytics\HomeCredit;
use App\Classes\Analytics\Eurasian;
use App\Classes\Analytics\Tinkoff;
use App\User;
use App\Account;
use App\Trainee;
use App\UserDescription;
use App\UserNotification;
use App\Kpi;
use App\Zarplata;
use App\DayType;
use App\Salary;
use App\ProfileGroup;
use App\CallBase;
use App\Timetracking;
use App\TimetrackingHistory;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\Analytics\Activity;
use App\Models\Analytics\ActivityTotal;
use App\Models\Analytics\ActivityPlan;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use App\QualityRecordMonthlyStat;
use App\Models\CallCenter\Directory;
use App\Models\CallCenter\Agent;
use App\Models\Analytics\RecruiterStat;
use App\Classes\Analytics\FunnelTable;
use App\Models\User\NotificationTemplate;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\DecompositionItem;
use App\Models\Analytics\TopValue;
use App\QualityRecordWeeklyStat;
use App\Models\Admin\Bonus;
use App\Models\Admin\ObtainedBonus;
use App\Models\Admin\EditedKpi;
use App\Models\Admin\EditedBonus;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\Imports\AnalyticsImport;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    
    public function __construct()
    {
        View::share('title', 'Аналитика групп');
        $this->middleware('auth');
    }

    /**
     * Permission control
     */
    public function redirect_if_has_not_permission() {
        if(!auth()->user()->can('analytics_view')) {
            return redirect('/');
        }
    }

    public function index()
    {
        $this->redirect_if_has_not_permission();

        
        $groups = ProfileGroup::whereIn('has_analytics', [0,1]);


        $_groups = [];
    
        if(auth()->user() && auth()->user()->is_admin == 1) $groups->whereIn('id', auth()->user()->groups);
            
        $groups = $groups->where('active', 1)->get();
    
        //if(auth()->id() == 18) dd($groups);
        if(auth()->user()->is_admin != 1) {
            foreach ($groups as $key => $group) {
                $editors_id = $group->editors_id == null ? [] : json_decode($group->editors_id);
                if(!in_array(auth()->id(), $editors_id))  continue;
                $_groups[] = $group;
            }
            $groups = $_groups;
        }
       
      
        View::share('menu', 'timetrackinganalytics');
        return view('admin.analytics-page', compact('groups'));
    }

    /**
     * Axios get analytics
     */
    public function get(Request $request)
    {
        $group_id = $request->group_id;
        $month = $request->month;
        $year = $request->year;
        $date = Carbon::createFromDate($year, $month, 1);
        
        $group = ProfileGroup::find($group_id);
        $currentUser = User::bitrixUser();

        $superusers = User::where('is_admin', 1)->get(['id'])->pluck('id')->toArray();

        if(!in_array(Auth::user()->id, $superusers)) {

            $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
            // Доступ к группе
            if (!$group || !in_array($currentUser->id, $group_editors) && $currentUser->id != 18) {
                return [
                    'error' => 'access',
                ];
            }
        }
 

        $ac = AnalyticColumn::where('group_id', $group_id)->first();
        $ar = AnalyticRow::where('group_id', $group_id)->first();
        if(!$ac || !$ar) return [
            'error' => 'No analytics',
            'archived_groups' => ProfileGroup::where('has_analytics', -1)->where('active', 1)->get(),
            'groups' => ProfileGroup::whereIn('has_analytics', [0,1])->where('active', 1)->get(),
        ];

        
        // utility and rentability
        $util = TopValue::getUtilityGauges($date->format('Y-m-d'), [$group_id]);
        $rent = TopValue::getRentabilityGauges($date->format('Y-m-d'), [$group_id], 'Рентабельность');
        if(count($util) > 0) {
            $util[0]['gauges'] = array_merge($util[0]['gauges'], $rent);
        }   
        
        
        $call_bases = [];
        if($group_id == 53) {
            $call_bases = CallBase::formTable($date->format('Y-m-d'));
        }


        // Ed 

        $ffp = Recruiting::getFiredInfo($date->subMonth()->format('Y-m-d'), $group_id);
        $ff = Recruiting::getFiredInfo($date->addMonth()->format('Y-m-d'), $group_id);
        $fired_percent_prev = $ffp['percent'];
        $fired_percent = $ff['percent'];
        $fired_number_prev = $ffp['fired'];
        $fired_number = $ff['fired'];
       
        //dd(AnalyticStat::form($group_id, $date->format('Y-m-d')));


        $groups = ProfileGroup::whereIn('has_analytics', [0,1])->where('active', 1)->get();

      
        if(auth()->user()->is_admin != 1) {
            $_groups = [];
            foreach ($groups as $key => $group) {
                $editors_id = json_decode($group->editors_id); 
                if ($group->editors_id == null) $editors_id = [];
                if(!in_array(auth()->id(), $editors_id))  continue;
                $_groups[] = $group;
            }
            $groups = $_groups;
        } 
        
        return [
            'decomposition' => DecompositionValue::table($group_id, $date->format('Y-m-d')),
            'activities' => UserStat::activities($group_id, $date->format('Y-m-d')),
            'table' => AnalyticStat::form($group_id, $date->format('Y-m-d')),
            'columns' => AnalyticStat::columns($group_id, $date->format('Y-m-d')),
            'utility' => $util,
            'totals' => [],
            'groups' => $groups,
            'archived_groups' => ProfileGroup::where('has_analytics', -1)->where('active', 1)->get(),
            'call_bases' => $call_bases,
            'fired_percent_prev' => $fired_percent_prev,
            'fired_percent' => $fired_percent,
            'fired_number_prev' => $fired_number_prev,
            'fired_number' => $fired_number,
        ];
    }

    /**
     * saveCellActivity
     */
    public function saveCellActivity(Request $request) {
        //dd($request->all());

        $start = Carbon::createFromDate($request->year,$request->month, 1)->format('Y-m-d');
        $columns = AnalyticColumn::where('date', $start)
            ->where('group_id', $request->group_id)
            ->whereNotIn('name', ['name','sum','avg', 'plan'])
            ->get();

        
        
        foreach ($columns as $key => $column) {
            $date = Carbon::createFromDate($request->year,$request->month, $column->name)->format('Y-m-d');

            $stat = AnalyticStat::where('date', $start)
                ->where('row_id', $request->row_id)
                ->where('column_id', $column->id)
                ->first();

            if($stat) {
                $total_for_day = UserStat::total_for_day($request->activity_id, $date);
                
                $total_for_day = floor($total_for_day * 10) / 10;
                
                $stat->value = $total_for_day;
                $stat->show_value = $total_for_day;
                $stat->type = 'stat';
                $stat->class = $request->class;
                $stat->activity_id = $request->activity_id;
                $stat->save(); 
            }
        }
    }

    /**
     * saveCellTime
     */

    public function saveCellTime(Request $request) {
      
        $start = Carbon::createFromDate($request->year,$request->month, 1)->format('Y-m-d');
        $columns = AnalyticColumn::where('date', $start)
            ->where('group_id', $request->group_id)
            ->whereNotIn('name', ['name','sum','avg', 'plan'])
            ->get();
        
        foreach ($columns as $key => $column) {
            $date = Carbon::createFromDate($request->year,$request->month, $column->name)->format('Y-m-d');

            $stat = AnalyticStat::where('date', $start)
                ->where('row_id', $request->row_id)
                ->where('column_id', $column->id)
                ->first();

            if($stat) {
                $total_for_day = Timetracking::totalHours($date, $request->group_id);
                
                $total_for_day = floor($total_for_day / 9 * 10) / 10;

                $stat->value = $total_for_day;
                $stat->show_value = $total_for_day;
                $stat->type = 'time';
                $stat->class = $request->class;
                $stat->save(); 
            } 
        }
    }

    /**
     * saveCellActivity
     */
    public function saveCellSum(Request $request) {

        $date = Carbon::createFromDate($request->year,$request->month, 1)->format('Y-m-d');

        $stat = AnalyticStat::where('date', $date)
            ->where('row_id', $request->row_id)
            ->where('column_id', $request->column_id)
            ->first();

        $total_for_day = 0;
        if($stat) {
            $total_for_day = AnalyticStat::daysSum($date, $request->row_id, $request->group_id);
           
            $stat->value = $total_for_day;
            $stat->show_value = $total_for_day;
            $stat->type = 'sum';
            $stat->class = $request->class;
            $stat->save(); 
        }

        return $total_for_day;
    }

    /**
     * saveCellActivity
     */
    public function saveCellAvg(Request $request) {

        $date = Carbon::createFromDate($request->year,$request->month, 1)->format('Y-m-d');

        $stat = AnalyticStat::where('date', $date)
            ->where('row_id', $request->row_id)
            ->where('column_id', $request->column_id)
            ->first();

        $total_for_day = 0;
        if($stat) {
            $total_for_day = AnalyticStat::daysAvg($date, $request->row_id, $request->group_id);
           
            $stat->value = $total_for_day;
            $stat->show_value = $total_for_day;
            $stat->type = 'avg';
            $stat->class = $request->class;
            $stat->save(); 
        }

        return $total_for_day;
    }

    /**
     * Add row to group
     */
    public function addRow(Request $request) {
        $date = $request->date;
        $group_id = $request->group_id;
        
        return AnalyticStat::new_row($group_id, $request->after_row_id, $date);
    }

    public function removeDependency(Request $request) {
        $row = AnalyticRow::where('id', $request->id)->first();

        if($row) {
            $row->depend_id = null;
            $row->save();
        }
    }

    
    /**
     * Delete row from group
     */
    public function deleteRow(Request $request) {
        $date = $request->date;
        $group_id = $request->group_id;
        $item = $request->item;

        $row_id = $item['name']['row_id'];
        $row = AnalyticRow::find($row_id);
        $row->delete();
        return 'Deleted';
    }

    /**
     * Edit stat
     */
    public function editStat(Request $request) {

        $stat = AnalyticStat::where('date', $request->date)
            ->where('row_id', $request->row_id)
            ->where('column_id', $request->column_id)
            ->first();

        
        if($stat) {
            $old_value =  $stat->value;
            $stat->value = $request->value;
            $stat->show_value = $request->show_value;

            if($request->type == 'formula') {
                $stat->value = $request->formula;
            }

            if($request->type == 'remote' || $request->type == 'inhouse') {

                if($request->type == 'remote') {
                    $type = 'remote';
                } else {
                    $type = 'office';
                }

                $day = AnalyticColumn::withTrashed()->find($request->column_id);
                if($day) {
                    $date = Carbon::parse($request->date)->day($day->name)->format('Y-m-d');
                    $this->addHours($request->group_id, $type, $request->value, $old_value, $date);
                }

                $stat->comment = $request->comment;

            }

            $stat->type = $request->type;
            // if($request->value == 0 || isset($request->value)){
            //    $stat->class = 'text-center text-center'; 
            // }
            // else{
                
            // }
            $stat->class = $request->class;
            $stat->save(); 
        }
    }

    public function addHours($group_id, $user_type, $value, $old_value, $date) {
            $group_users = json_decode(ProfileGroup::find($group_id)->users);
            $tts = Timetracking::whereIn('user_id', $group_users)
                ->whereDate('enter', $date)
                ->orderBy('enter', 'desc')
                ->get();

            
            $marked_users = [];


            foreach($tts as $tt) {
                $user = User::find($tt->user_id);
                if(!$user)continue;
                if(!$user->user_type)continue;
                if($user->user_type != $user_type) continue;

                if(!in_array($tt->user_id, $marked_users)) {

                    $new_value = $tt->total_hours + $value - $old_value;
                    if($new_value < 0) $new_value = 0;
                    $tt->total_hours = $new_value; 

                    $tt->updated =  1;
                    $tt->save();
                    
                    array_push($marked_users, $tt->user_id);

                    if($value == 0) {
                        $desc = 'Отмена: Минуты за "Отсутствие связи"';
                    } else {
                        $old_text = $old_value != 0 ? ', минус предыдущие добавленные ' . $old_value . ' минут' : '';
                        $desc = 'Отсутствие связи. <br> Добавлено '. $value . ' минут ' . $old_text;
                    }

                    TimetrackingHistory::create([
                        'user_id' => $tt->user_id,
                        'author_id' => Auth::user()->id,
                        'author' => Auth::user()->last_name . ' ' . Auth::user()->name,
                        'date' => $date,
                        'description' => $desc,
                    ]);
                        
                    
                }
            }
       
    }

     /**
     * new group analtyics
     */
    public function newGroup(Request $request) {
        
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        AnalyticColumn::defaults($request->group_id, $date);
        AnalyticRow::defaults($request->group_id, $date);
        $group = ProfileGroup::find($request->group_id);
        if($group) {
            $group->has_analytics = 1;
            $group->save();
        }

       
        Activity::createQuality($request->group_id);

    }
    
    public function createActivity(Request $request) {

        $act = Activity::create([
            'name' => $request->activity['name'], 
            'group_id' => $request->group_id,
            'daily_plan' => $request->activity['daily_plan'], 
            'plan_unit' => $request->activity['plan_unit'], 
            'unit' => $request->activity['unit'], 
            'weekdays' => $request->activity['weekdays'], 
            'ud_ves' => 0
        ]);

        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        return UserStat::activities($request->group_id, $date);
    }

    public function editActivity(Request $request) {
        $act = Activity::find($request->activity['id']);
        if($act) {
            $act->update([
                'name' => $request->activity['name'], 
                'daily_plan' => $request->activity['daily_plan'], 
                'plan_unit' => $request->activity['plan_unit'], 
                'unit' => $request->activity['unit'], 
                'weekdays' => $request->activity['weekdays'], 
            ]);
        }
    }

    /**
     * Edit User Stat value
     */
    public function updateUserStat(Request $request) {

        $group = ProfileGroup::find($request->group_id);
        $date = Carbon::createFromDate($request->year, $request->month, $request->day)->format('Y-m-d');

        $us = UserStat::where('date', $date)
            ->where('user_id', $request->employee_id)
            ->where('activity_id', $request->id)
            ->first();

        if($us) {
            $us->value = $request->value;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $request->employee_id,
                'activity_id' => $request->id,
                'value' => $request->value,
            ]);
        }
        
        if($group->time_address == $request->id && !in_array($request->employee_id, $group->time_exceptions)) {
            Timetracking::updateTimes($request->employee_id, $date, $request->value * 60);
        }

        if($request->group_id == 31 && $request->id == 20) { // DM and 20 колво действий
            DM::updateTimesNew($request->employee_id, $date);
        }

        if($request->group_id == 31 && $request->id == 21) {
            DM::updateTimesByWorkHours($request->employee_id, $date, $request->day, (float)$request->value);
        }
    }
    
    public function change_order(Request $request) {
        $count = count($request->activities);
        foreach ($request->activities as $index => $activity) {
            $act = Activity::find($activity['id']);
            $act->order = $count--;  
            $act->save();
        }
    }

    public function delete_activity(Request $request) {

            $act = Activity::find($request['id']);
            $act->delete();
        
    }

    public function setDecimals(Request $request) {
        $stat = AnalyticStat::where('column_id', $request->column_id)
            ->where('row_id', $request->row_id)
            ->first();

        
            if($stat) {
                $stat->decimals = $request->decimals;
                $stat->save();
            }
    
    }
    

    public function add_depend(Request $request) {
        $row = AnalyticRow::find($request->id);
        if($row) {
            $row->depend_id = $request->depend_id;
            $row->save();
        }
    }

    public function archive_analytics(Request $request) {
        $group = ProfileGroup::find($request->id);
        if($group) {
            $group->has_analytics = -1;
            $group->save();
        }
    }

    public function restore_analytics(Request $request) {
        $group = ProfileGroup::find($request->id);
        if($group) {
            $group->has_analytics = 1;
            $group->save();
        }
    }

    public function addRemoteInhouse(Request $request) {
        $date = $request->date;
        $formula_row = AnalyticRow::find($request->row_id);

        $rows = AnalyticRow::where('group_id', $formula_row->group_id)->get();
        $days = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
        $columns = AnalyticColumn::where('group_id', $formula_row->group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        foreach ($columns as $key => $column) {
            $stat = AnalyticStat::where('row_id', $formula_row->id)
                ->where('column_id', $column->id)
                ->first();
            
            if($stat) {
                $stat->update([
                    'row_id' => $formula_row->id,
                    'column_id' => $column->id,
                    'value' => 0,
                    'show_value' => 0,
                    'type' => $request->type,
                    'class' => 'text-center',
                    'editable' => 1,
                ]);
            } else {
                self::create([
                    'group_id' => $formula_row->group_id,
                    'date' => $date,
                    'row_id' => $formula_row->id,
                    'column_id' => $column->id,
                    'value' => 0,
                    'show_value' => 0,
                    'type' => $request->type,
                    'class' => 'text-center',
                    'editable' => 1,
                ]);
            }
        }

    }

    public function addFormula_1_31(Request $request) {
        
        $date = $request->date;

        $formula_row = AnalyticRow::find($request->row_id);
        $rows = AnalyticRow::where('group_id', $formula_row->group_id)->get();
        
        $days = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];

        $columns = AnalyticColumn::where('group_id', $formula_row->group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        foreach ($columns as $key => $column) {
            $stat = AnalyticStat::where('row_id', $formula_row->id)
                ->where('column_id', $column->id)
                ->first();
            
            $formula = $request->formula;
            foreach ($rows as $key => $row) { 
                $formula = str_replace("{". $row->id ."}", "[". $column->id .":". $row->id ."]", $formula);
            }
            
            if($stat) {
                $stat->update([
                    'row_id' => $formula_row->id,
                    'column_id' => $column->id,
                    'value' => $formula,
                    'show_value' => 0,
                    'type' => 'formula',
                    'class' => 'text-center',
                    'decimals' => $request->decimals,
                    'editable' => 1,
                ]);
            } else {
                self::create([
                    'group_id' => $formula_row->group_id,
                    'date' => $date,
                    'row_id' => $formula_row->id,
                    'column_id' => $column->id,
                    'value' => $formula,
                    'show_value' => 0,
                    'type' => 'formula',
                    'class' => 'text-center',
                    'decimals' => $request->decimals,
                    'editable' => 1,
                ]);
            }
        }

    }


    /**
     * Экспорт активностей (Подробная аналитика) группы в Excel
     */
    public function exportActivityExcel(Request $request){
        
        $group = ProfileGroup::find($request->group_id);
        
        $request->month = (int) $request->month;
        $currentUser = User::bitrixUser();

        $group_editors = is_array(json_decode($group->editors_id)) ? json_decode($group->editors_id) : [];
        // Доступ к группе 

 
        if (!auth()->user()->can('analytics_view') && !in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        // $group_accounts = DB::connection('callibro')->table('call_account')
        //         ->where('owner_uid', 5)->whereIn('email', $users_emails)
        //         ->groupBy('call_account.id')
        //         ->orderBy('full_name')
        //         ->get(['id', 'email', 'name', 'surname', DB::raw("CONCAT(surname,' ',name) as full_name")]);
        
        $this->users = User::withTrashed()->whereIn('id', json_decode($group->users))
        ->get(['ID as id', 'email as email', 'name as name', 'last_name as surname', DB::raw("CONCAT(last_name,' ',name) as full_name")]);;

        /****************************** */
        /******==================================== */
        $date = Carbon::createFromDate($request->year, $request->month, 1);


        $title = 'Аналитика активностей ' . $request->month . ' месяц '. $request->year;

        $data = UserStat::activities($request->group_id, $date->format('Y-m-d'));

        /******==================================== */
        
        $sheets = [];

        $minute_headings = Activity::getHeadings($date, Activity::UNIT_MINUTES);
        $percent_headings = Activity::getHeadings($date, Activity::UNIT_PERCENTS);
      
        foreach($data as $sheet_content){
            $sheets[] = [
                    'title' => $sheet_content['name'],
                    'headings' => $minute_headings,
                    'sheet' => Activity::getSheet($sheet_content['records'], $date, Activity::UNIT_MINUTES)
            ];
        }
       

        /******==================================== */

        if(ob_get_length() > 0) ob_clean(); //  ob_end_clean();

        if($date->daysInMonth == 28) $last_cell = 'AH3';
        if($date->daysInMonth == 29) $last_cell = 'AI3';
        if($date->daysInMonth == 30) $last_cell = 'AJ3';
        if($date->daysInMonth == 31) $last_cell = 'AK3';

        return Excel::download(new AnalyticsImport($sheets,$group), $title .' "'.$group->name . '".xls');
        
    }
    
}

