<?php

namespace App\Http\Controllers\Analytics;

use View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProfileGroup;
use App\Models\Analytics\TopValue;
use App\Models\Analytics\TopEditedValue;
use App\Classes\Analytics\Recruiting;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\Activity;
use App\Models\Analytics\CustomProceed;

class TopController extends Controller
{   
    /**
     * Id групп
     */
    public $groups;

    public function __construct()
    {
        View::share('title', 'ТОП');
        $this->middleware('auth');
    }

    /**
     * Страница аналитика группы
     */
    public function index()
    {   
        View::share('menu', 'timetrackingtop');
        if(!auth()->user()->can('top_view')) {
            return redirect('/');
        }

        $date = Carbon::now()->startOfMOnth()->format('Y-m-d');

        return view('admin.top')->with([
            'data' => [
                'rentability' => TopValue::getRentabilityGauges($date),
                'utility' => TopValue::getUtilityGauges($date),
                'proceeds' => $this->getProceeds($date),
                'prognoz_groups' => Recruiting::getPrognozGroups($date),
            ],
        ]);
    }

    public function fetch(Request $request)
    {   
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        return response()->json([
            'rentability' => TopValue::getRentabilityGauges($date),
            'utility'  => TopValue::getUtilityGauges($date),
            'proceeds' => $this->getProceeds($date),
        ]);
        
    }

    /**
     * Сводная таблица рентабельности за год
     */
    public function getRentability(Request $request) 
    {
        return TopValue::getPivotRentability($request->year, $request->month);
    }

    /**
     * Сводная таблица рентабельности только за месяц
     */
    public function getRentabilityOnMonth(Request $request) 
    {
        return TopValue::getPivotRentabilityOnMonth($request->year, $request->month);
    }
    
    /**
     * Таблица выручки колл центра
     */
    private function getProceeds($date)
    {
        $calendar_days = Carbon::parse($date)->daysInMonth; // календарные дни

        $days = $this->daysInMonth($date);
        // get weeks array 
        $weeks = [];
        
        $start_week = 1;
        if($days[0]->dayOfWeek == 1) $start_week = 0;
        
        foreach($days as $key => $date) {
            if($date->dayOfWeek == '1') {
                $start_week++;
            }
            $weeks[$start_week][] = $date;
        }

        $week_proceeds = [
            'fields' => $this->getProceedFields($days),
            'records' => [],
        ];
        
        $total_row = [];
        $total_row['Отдел'] = 'Итого';
        $total_row['План'] = 0;
        $total_row['Итого'] = 0;

        foreach($weeks as $key => $week) {
            $total_row['w'.$key] = 0;
        }

        foreach($days as $date) {
            $total_row[$date->format('d.m')] = 0;
        }

        $this->groups = ProfileGroup::profileGroupsWithArchived($date->year, $date->month, true, true, ProfileGroup::SWITCH_PROCEEDS);

        foreach($this->groups as $group_id) {
            $group = ProfileGroup::find($group_id);
            $row = [];

            $firstDayMonth = Carbon::create($date->year, $date->month)->firstOfMonth()->format('Y-m-d');
            if ($group->archived_date && $group->archived_date >= $firstDayMonth) {
                $firstDayNextMonth = Carbon::create($date->year, $date->month + 1)->firstOfMonth()->format('Y-m-d');
                $row["deleted_at"] = $firstDayNextMonth;
            }

            if($group) {

                $row['Отдел'] = $group->name;
                $row['group_id'] = $group->id;

                $sum = 0;
                $filled_days = 0;

                $prs = AnalyticStat::getProceeds($group_id, $date);
                $plan = AnalyticStat::getProceedsPlan($group_id, $date);
                
                $row['%'] = 2;
                $row['План'] = $plan;
                $row['+/-'] = 2;
                $total_row['План'] += $plan;
                
                
                
                foreach($weeks as $key => $week) {
                    $xsum = 0;
                    foreach($week as $date) {
                        $row[$date->format('d.m')] = (int)$prs[$date->day];
                        if((int)$prs[$date->day] > 0) $filled_days++;
                        $sum += $prs[$date->day];
                        $total_row[$date->format('d.m')] += $prs[$date->day];
                        $xsum += $prs[$date->day];
                    }
                    $row['w'. ($key)] = round($xsum);
                    $total_row['w'. $key] += round($xsum);
                }

            
                $row['Итого'] = (int)$sum;
                $total_row['Итого'] += $sum;

                $row['+/-'] = '';
                $total_row['+/-'] = '';

                $row['%'] = '';
                $total_row['%'] = '';
                
                if($filled_days > 0 && $plan > 0) {
                    $row['+/-'] = (int)$sum / ($plan / $calendar_days * $filled_days) * 100;
                    $row['+/-'] = (round($row['+/-'],1) - 100) . '%';

                    $row['%'] = ((int)$sum * 100) / $plan;
                    $row['%'] = round($row['%'],1) . '%';
                }
                
                

                array_push($week_proceeds['records'], $row);
            }
              
        }

        $total_row['План'] = (int)$total_row['План'];
        $total_row['Итого'] = (int)$total_row['Итого'];
        

        $cps = CustomProceed::whereYear('date', $days[0]->year)
            ->whereMonth('date', $days[0]->month)
            ->get()
            ->groupBy('order');

        foreach ($cps as $order => $cpo) {
            // editable row with inputs
            $editable_row = [];
            $editable_row['Отдел'] = $cpo->first() ? $cpo->first()->name : '';
            $editable_row['План'] = 0;
            $editable_row['Итого'] = 0;
            
            foreach($weeks as $key => $week) {
                $sum = 0;
                foreach($week as $date) { 
                    $d = $cpo->where('date', $date->format('Y-m-d'))->first();
                    
                    $val = $d ? (int)$d->value : 0;
                    $sum += $val;
                    $editable_row[$date->format('d.m')] = $val;
                    $editable_row['Итого'] += $val;
                    $total_row[$date->format('d.m')] += $val;
                }
                
                $editable_row['group_id'] =  0 - $order;
                $editable_row['w'. ($key)] = $sum;

            }
            
            array_push($week_proceeds['records'], $editable_row);  
        
        
            $total_row['Итого'] += $editable_row['Итого'];
        }
        
        foreach($days as $date) { 
            $total_row[$date->format('d.m')] = (int)number_format($total_row[$date->format('d.m')], 0);
        }
        
        array_push($week_proceeds['records'], $total_row);  

        return $week_proceeds;
    }

    private function getProceedFields($days)
    {
        $fields = [];
        array_push($fields, 'Отдел');
        array_push($fields, '%');
        array_push($fields, 'План');
        array_push($fields, 'Итого');
        array_push($fields, '+/-');

        // get weeks  
        $start_week = 1;
        $last_week = null;

        foreach($days as $key => $date) {
            
            // define first week field
            if(
                $date->dayOfWeek == '1'
                || ($key == 0 && $date->dayOfWeek != '1')
            ) {
                if($last_week != null) {
                    array_push($fields, $last_week);
                    $last_week = null;
                }
                $last_week = 'w' . $start_week;
                $start_week++;
            }
            
            // push day
            array_push($fields, $date->format('d.m'));

            // push last week field because we didn't reach monday
            if(count($days) - 1 == $key && $date->dayOfWeek != '1') {
                array_push($fields, $last_week);
            }
        }

    
        return $fields;
    }

    /**
     * Даты для таблицы выручки
     */
    private function daysInMonth($date)
    {
        $date = Carbon::parse($date)->startOfMonth();
        
        $days = [];

        for($i = 1; $i <= $date->daysInMonth ; $i++) {
            array_push($days, Carbon::parse($date->day($i)->format('Y-m-d')));
        }

        return $days;
    }

    public function saveRentMax(Request $request) 
    {
        $gauge = $request->gauge;

        $group = ProfileGroup::find($gauge['group_id']);
        
        $group->rentability_max = $gauge['max_value'];
        $group->save();
    }

    public function saveGroupPlan(Request $request) 
    {
        $group = ProfileGroup::find($request->group_id);
        
        $group->required = $request->plan;
        $group->save();
    }
    
    public function saveTopValue(Request $request)
    {

        $gauge = $request->gauge;

        $top_value = TopValue::find($gauge['id']);

        if($top_value) {
           
            $top_value->value = $gauge['value'];
            $top_value->name = $gauge['name'];
            $top_value->value_type = $gauge['value_type'];
            $top_value->round = $gauge['round'];
            $top_value->is_main = $gauge['is_main'];

            if($gauge['is_main'] == 1) {
                $tvs = TopValue::where('group_id', $top_value->group_id)->where('date', $top_value->date)->update(['is_main' => 0]);
            }

            $top_value->min_value = $gauge['min_value'];
            $top_value->cell = $gauge['cell'];
            $top_value->activity_id = $gauge['activity_id'];
            $top_value->max_value = $gauge['max_value'];
            $top_value->reversed = $gauge['reversed'];
            $top_value->options = json_encode($gauge['options']);
            $top_value->unit = $gauge['unit'] ? $gauge['unit'] : '';
            $top_value->save();
            
            $value = $top_value->value;
            if($top_value->activity_id != 0 && $top_value->activity_id != -1) {
                $value = UserStat::total_for_month($top_value->activity_id, $top_value->date, $top_value->value_type);
            }

            $options = TopValue::getDynamicValue($top_value->group_id, $top_value->date, $top_value)['options'];


            return ['code' => 200, 'value' => $value, 'options' => $options];
        } else {
            return ['code' => 404];
        }

    }
    
    public function getActivities(Request $request)
    {
        return Activity::where('group_id', $request->group_id)->get();
    }

    public function createGauge(Request $request)
    {
        $group_id = $request->group_id;
        $activity_id = $request->activity_id;

        $top_value = new TopValue();

        $top_value->activity_id = $activity_id;
        $top_value->name = $request->name;
        $top_value->group_id = $group_id;
        $top_value->date = Carbon::now()->day(1)->format('Y-m-d');
        $top_value->value = 0;
        $top_value->min_value = 0;
        $top_value->max_value = 100;
        $top_value->cell = $request->cell;
        $top_value->value_type = $request->value_type;
        $top_value->unit = '';
        $top_value->options = '[]';
        $top_value->options = json_encode($top_value->getOptions());
        $top_value->save();

        return [
            'id' => $top_value->id,
            'name' => $top_value->name,
            'value' => $top_value->value,
            'group_id' => $top_value->group_id,
            'place' => 1,
            'unit' => $top_value->unit,
            'editable' => true,
            'edit_value' => true,
            'key' => $top_value->id * 1000,
            'min_value' => $top_value->min_value,
            'activity_id' => $top_value->activity_id,
            'max_value' => $top_value->max_value,
            'cell' => $top_value->cell,
            'sections' => '[0,50,75,100]', 
            'options' => json_decode($top_value->options),
            'diff' => 0
        ];
    }

    public function deleteGauge(Request $request)
    {
        $tv = TopValue::find($request->gauge['id']);
        if($tv) {
            $tv->delete();
        }
    }

    public function updateTopEditedValue(Request $request)
    {
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        $tev = TopEditedValue::where('group_id', $request->group_id)
            ->where('date', $date)
            ->first();
            
        if($tev) {
            if($request->value) {
                $tev->value = $request->value;
                $tev->save();
            } else {
                $tev->delete();
            }
            
        } else {
            TopEditedValue::create([
                'date' => $date,
                'group_id' => $request->group_id,
                'value' => $request->value,
            ]);
        }
    }

    public function updateProceeds(Request $request)
    {
        $date = explode('.', $request->date);
  
        $day = (int)$date[0];
        $month = (int)$date[1];
        $year = $request->year;

        $date = Carbon::createFromDate($request->year, $month, $day)->format('Y-m-d');

        $cp = CustomProceed::where('date', $date)->where('order', abs($request->group_id))->first();

        if($cp) {
            $cp->value = $request->value;
            $cp->name = $request->name;
            $cp->save();
        } else {    
            CustomProceed::create([
                'date' => $date,
                'name' => $request->name,
                'order' => abs($request->group_id),
                'value' => $request->value,
            ]);
        }

        $cps = CustomProceed::where('date', $date)
            ->where('order', abs($request->group_id))
            ->get();

        foreach ($cps as $key => $cp) {
            $cp->name = $request->name;
            $cp->save();
        }
     
    }  
}

