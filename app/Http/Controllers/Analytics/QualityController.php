<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Service\Department\UserService;
use App\Models\CheckReports;
use App\ProfileGroup;
use App\User;
use App\QualityParam;
use App\QualityParamValue;
use App\QualityRecord;
use App\QualityRecordWeeklyStat;
use App\QualityRecordMonthlyStat;
use App\Models\Analytics\UserStat;
use App\Models\CallibroDialer;
use App\UserNotification;

class QualityController extends Controller
{
    public function __construct()
    {
        \View::share('title', 'Отдел Контроля Качества');
        \View::share('menu', 'timetrackingqc');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $check = isset($request['check']) ? $request['check'] : 1;
        if(!auth()->user()->can('quality_view')) {
            return redirect('/');
        }

        $groups = ProfileGroup::where('active', 1)->get();
        $group_id = $groups->count() > 0 ? $groups->first()->id : 0;
        if(isset($request['user_id'])){
            $user = User::find($request['user_id']);
            $userGroups = $user->inGroups();
            $group_id = $userGroups->count() > 0 ? $userGroups->first()->id : 0;
        }

        return view('admin.quality_control',
            compact('groups','group_id','check'));
    }

    public function getRecords(Request $request)
    {
        $currentUser = auth()->user();
        $group = ProfileGroup::find($request->group_id);

        if(!$group)  return [
            'error' => 'access',
        ];

        $editors_id = json_decode($group->editors_id);
        $group_editors = is_array($editors_id) ? $editors_id : [];

        if (auth()->user()->is_admin != 1 && !in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        $working = (new UserService)->getEmployees($request->group_id, $date);
        $working = collect($working)->pluck('id')->toArray();

        $fired = (new UserService)->getEmployees($request->group_id, $date);
        $fired = collect($fired)->pluck('id')->toArray();

        $user_ids = array_unique(array_merge($working, $fired));

        $raw_items = User::withTrashed()->whereIn('id', $user_ids)->orderBy('last_name', 'asc')->select(['id','last_name', 'name'])->get();

        $items = [];

        // CREATE WEEKS ARRAY
        $weeks = $this->weeksArray($request->month, $request->year);

        foreach($raw_items as $raw_item) {
            $item = [];

            $item['name'] = $raw_item->last_name. ' ' . $raw_item->name;
            $item['id'] = $raw_item->id;

            // FETCHING WEEKS DATA
            $week_totals = QualityRecordWeeklyStat::where([
                    'user_id' => $raw_item->id,
                    'month' => $request->month,
                    'year' => $request->year,
                    'group_id' => $group->id
                ])->get();

            foreach($week_totals as $week) {
                $item['weeks'][$week->day] = $week->total;
            }

            ///// COUNT WEEKS TOTALS
            $item['weeks']['total'] = 0;
            $actual_weeks = 0;

            foreach($weeks as $key => $value) {
                $avg = 0;
                $count = 0;

                foreach($value as $val){
                    if(isset($item['weeks'][$val]) && $item['weeks'][$val]) {
                        $avg += $item['weeks'][$val];
                        $count++;
                    }
                }

                if($count > 0) {
                    $result = round($avg / $count);
                    $item['weeks']['avg'.$key] = $result;
                    $item['weeks']['total'] += $result;
                    $actual_weeks++;
                }
            }

            if($actual_weeks > 0) {
                $item['weeks']['total'] = round($item['weeks']['total'] / $actual_weeks);

                $monthly = QualityRecordMonthlyStat::where([
                    'month' => $request->month,
                    'year' => $request->year,
                    'user_id' => $raw_item->id,
                    'group_id' => $group->id
                ])->first();

                if($monthly) {
                    $monthly->total = (int)$item['weeks']['total'];
                    $monthly->save();
                } else {
                    QualityRecordMonthlyStat::create([
                        'month' => $request->month,
                        'year' => $request->year,
                        'total' => (int)$item['weeks']['total'],
                        'user_id' => $raw_item->id,
                        'group_id' => $group->id
                    ]);
                }
            }


            // FETCHING MONTHS DATA
            $month_totals = QualityRecordMonthlyStat::where([
                'user_id' => $raw_item->id,
                'year' => $request->year,
                'group_id' => $group->id
            ])->get();

            foreach($month_totals as $mon) {
                $item['months'][$mon->month] = $mon->total;
            }
            ///// COUNT MONTHS

            $item['months']['total'] = 0;

            $count = 0;
            $sum = 0;
            for($m=1;$m<=12;$m++) {

                if(isset($item['months'][$m])) {
                    $sum += $item['months'][$m];
                    if($item['months'][$m] > 0) $count++;
                }
            }

            if($count == 0) {
                $item['months']['total'] = 0;
            } else {
                $item['months']['total'] = round($sum / $count);
            }

            $item['months']['quantity'] = QualityRecord::whereYear('listened_on', $request->year)
                ->whereMonth('listened_on', $request->month)
                ->where('group_id', $group->id)
                ->where('employee_id', $raw_item->id)
                ->get()
                ->count();

            array_push($items, $item);
        }

        // Quality Record filling tab
        // Only for Kaspi

        if($group->quality == 'local') {
            $records = QualityRecord::whereYear('listened_on', $request->year)->whereMonth('listened_on', $request->month)->where('group_id', $group->id);

            if($request->employee_id != 0) {
                $records->where('employee_id', $request->employee_id);
            }

            if($request->day != 0){
                $records->whereDay('listened_on', $request->day);
            }

            $avg_month = 0;
            $avg_day = 0;
            if($request->employee_id != 0) {
                $user_month_records = QualityRecord::whereYear('listened_on', $request->year)
                    ->whereMonth('listened_on', $request->month)
                    ->where('employee_id', $request->employee_id)
                    ->where('group_id', $group->id)
                    ->orderBy('listened_on', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();

                $avg_month = $this->countAvgOfTotal($user_month_records);

                if($request->day != 0){
                    $user_day_records = QualityRecord::whereYear('listened_on', $request->year)
                        ->whereMonth('listened_on', $request->month)
                        ->whereDay('listened_on', $request->day)
                        ->where('group_id', $group->id)
                        ->where('employee_id', $request->employee_id)
                        ->orderBy('listened_on', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->get();

                    $avg_day = $this->countAvgOfTotal($user_day_records);
                }

            }

            $records_unique = collect($records->orderBy('listened_on', 'desc')->orderBy('created_at', 'desc')->get())->groupBy('employee_id');
            $records = $records->with('param_values')->orderBy('listened_on', 'desc')->orderBy('created_at', 'desc')->paginate(20);

            //////

            foreach($records->items() as $record) {
                $_user = User::withTrashed()->find($record->employee_id);
                if(is_null($_user)){
                    continue;
                }
                $record->name = $_user->last_name . ' ' . $_user->name;


                $record->dayOfDelay = $record->day_of_delay;
                $record->date = $record->listened_on;

                $_params = json_decode($record->params);

                // $record->param1 = (int)$_params[0];
                // $record->param2 = (int)$_params[1];
                // $record->param3 = (int)$_params[2];
                // $record->param4 = (int)$_params[3];
                // $record->param5 = (int)$_params[4];

                if($record->comments == null) $record->comments = '';


                if($record->segment_id == 1) $record->segment = '1-5';
                if($record->segment_id == 2) $record->segment = 'Напоминание';
                $record->editable = false;
            }
        }

        $null_records = [
            'current_page' => 1,
            'data' => [],
            'total' => 0,
        ];


        $q_params = QualityParam::where('group_id', $group->id)->where('active', 1)->get();




        $group = ProfileGroup::find($request->group_id);
        $check_users = CheckReports::getChecklistByGroup($group, $request);
        $dialer = CallibroDialer::where('group_id', $group->id)->first();





        return response()->json([
            'items' => $items,
            'records' => $group->quality == 'local' ? $records : $null_records,
            'records_unique' => $group->quality == 'local' ? $records_unique->count() : 0,
            'avg_day' => $group->quality == 'local' ? $avg_day : 0,
            'avg_month' => $group->quality == 'local' ? $avg_month : 0,
            'can_add_records' => $group->quality == 'local' ? true : false,
            'script_id' => $dialer ? $dialer->script_id : null,
            'dialer_id' => $dialer ? $dialer->dialer_id : null,
            'params' => $q_params,
            'check_users' => $check_users['check_users'] ?? null,
            'individual_type' => $check_users['individual_type'] ?? null,
            'individual_current' => $check_users['individual_current'] ?? null,
            'weeks' => $weeks,
        ]);
    }

    private function weeksArray($month, $year)
    {
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

    public function saveRecord(Request $request)
    {

        $user_id = auth()->id();




        if($request->id == 0) {

            $rec = QualityRecord::create([
                "employee_id" => $request->employee_id,
                "segment_id" => $request->segment_id,
                "interlocutor" => $request->interlocutor,
                "phone" => $request->phone ?? '0',
                "day_of_delay" => $request->dayOfDelay,
                "listened_on" => $request->date,
                'params' => '[0,0,0,0,0]',
                "total" => 0,
                "comments" => $request->comments,
                'user_id' => $user_id,
                'group_id' => $request->group_id
            ]);


            ////// total count
            $total = 0;
            foreach ($request->param_values as $key => $pv) {
                QualityParamValue::create([
                    'param_id' => $pv['param_id'],
                    'value' => $pv['value'],
                    'record_id' => $rec->id,
                ]);
                $total += (int)$pv['value'];
            }

            if($total > 100) {
                $total = 100;
            }

            $rec->total = $total;
            $rec->save();

            $this->getRank($total, $request);

            return response()->json([
                'method' => 'save',
                'id' => $rec->id,
                'total' => $total,
            ]);
        } else {
            $record = QualityRecord::find($request->id);

            $id = 0;

            if($record) {

                $total = 0;
                if(count($request->param_values) > 0 && array_key_exists('id', $request->param_values[0])) {
                     ////// total count

                    foreach ($request->param_values as $key => $pv) {
                        $param = QualityParamValue::find($pv['id']);

                        if($param) {
                            $param->value = $pv['value'];
                            $param->save();
                        } else {
                            QualityParamValue::create([
                                'param_id' => $pv['param_id'],
                                'value' => $pv['value'],
                                'record_id' => $record->id,
                            ]);
                        }
                        $total += (int)$pv['value'];
                    }
                } else {

                    QualityParamValue::where('record_id', $record->id)->delete();


                    foreach ($request->param_values as $key => $pv) {
                         QualityParamValue::create([
                                'param_id' => $pv['param_id'],
                                'value' => $pv['value'],
                                'record_id' => $record->id,
                            ]);

                        $total += (int)$pv['value'];
                    }
                }


                if($total > 100) {
                    $total = 100;
                }
                ///
                $record->update([
                    "employee_id" => $request->employee_id,
                    "segment_id" => $request->segment_id,
                    "interlocutor" => $request->interlocutor,
                    "phone" => $request->phone ?? '0',
                    "day_of_delay" => $request->dayOfDelay,
                    "listened_on" => $request->date,
                    'params' => '[0,0,0,0,0]',
                    "total" => $total,
                    "comments" => $request->comments,
                    'user_id' => $user_id
                ]);
                $id = $record->id;
            }
            $this->getRank($total, $request);

            return response()->json([
                'method' => 'update',
                'id' => $id,
            ]);
        }



    }

    public function deleteRecord(Request $request)
    {
        $record = QualityRecord::find($request->id);
        if($record) $record->delete();
    }

    public function exportExcel(Request $request)
    {

        $headings = [
            'Сотрудник',
            'Телефон',
            'День просрочки',
            'Собеседник',
            'Дата прослушки',

        ];



        $crits = QualityParam::where('group_id', $request->group_id)->get();
        foreach ($crits as $key => $crit) {
            array_push($headings, $crit->name);
        }

        array_push($headings, 'Совет');
        $data['records'] = [];

        $records = QualityRecord::whereYear('listened_on', $request->year)
                ->whereMonth('listened_on', $request->month)
                ->whereDay('listened_on', $request->day)
                ->where('group_id', $request->group_id)
                ->where('comments', '!=', null)
                ->orderBy('listened_on', 'desc')
                ->orderBy('created_at', 'asc')
                ->get()
                ->take(20);

            foreach($records as $record) {
                $_user = $record->user;
                $params = json_decode($record->params);
                $data['records'][] = [
                    0 => $_user->last_name . ' ' . $_user->name,
                    1 => $record->phone,
                    2 => strval($record->day_of_delay),
                    3 => $record->interlocutor,
                    4 => $record->listened_on,
                    5 => strval($params[0]),
                    6 => strval($params[1]),
                    7 => strval($params[2]),
                    8 => strval($params[3]),
                    9 => strval($params[4]),
                    10 => $record->comments ? $record->comments : '',
                ];
            }


        // на локале не работала эта функция, если оставил её на случай откатки
        ob_end_clean();
        if (ob_get_length() > 0) ob_clean();

        return Excel::create('Контроль_качества_за_'.$request->day.'.' .$request->month.'.'.$request->year, function ($excel) use ($data, $headings, $request) {
            $excel->setTitle('Отчет');
            $excel->setCreator('Laravel Media')->setCompany('MediaSend KZ');
            $excel->setDescription('Экспорт данных в Excel файл');
            $excel->sheet('Отчет за '.$request->day.'.' .$request->month.'.'.$request->year, function ($sheet) use ($data, $headings) {
                $sheet->fromArray($data['records'], null, 'A1', false, false);
                $sheet->prependRow(1, $headings);
            });
        })->export('xls');


    }

    private function countAvgOfTotal($records)
    {
        $sum_month = $records->sum('total');
        $count_month = $records->count();
            if($count_month == 0) {
                $avg_month = 0;
            } else {
                $avg_month = round($sum_month / $count_month);
            }
        return $avg_month;
    }

    public function saveWeeklyRecord(Request $request)
    {
        $rec = QualityRecordWeeklyStat::where([
            'day' => $request->day,
            'month' => $request->month,
            'year' => $request->year,
            'user_id' => $request->user_id,
            'group_id' => $request->group_id,
        ])->first();

        if($rec) {
            $rec->update(['total' => $request->total]);
        } else {
            $rec = QualityRecordWeeklyStat::create($request->all());
        }

        // save user_stats
        UserStat::saveQuality([
            'date'     => Carbon::createFromDate($request->year, $request->month, $request->day)->format('Y-m-d'),
            'user_id'  => $request->user_id,
            'value'    => $request->total,
            'group_id' => $request->group_id,
        ]);

        return $rec;
    }

    public function exportAllExcel(Request $request)
    {

        $segments = [
            1 => '1-5',
            2 => 'Нап',
            3 => '3160',
            4 => '6190',
            5 => 'ОВД',
            6 => '1-5 RED',
            7 => 'Нап RED',
            8 => '-',
            9 => '-',
            10 => 'ОВД RED',
            11 => '6_30 RED',
            12 => '6_30',
        ];

        $headings = [
            'Сотрудник',
            'Сегмент',
            'Телефон',
            'День просрочки',
            'Собеседник',
            'Дата прослушки',
        ];

        $crits = QualityParam::where('group_id', $request->group_id)->where('active', 1)->get();
        foreach ($crits as $key => $crit) {
            array_push($headings, $crit->name);
        }

        array_push($headings, 'Итого');
        array_push($headings, 'Комментарии');


        $data['records'] = [];

        $records = QualityRecord::whereYear('listened_on', $request->year)
                ->whereMonth('listened_on', $request->month)
                ->where('group_id', $request->group_id)
                ->orderBy('listened_on', 'desc')
                ->orderBy('created_at', 'asc')
                ->with('param_values')
                ->get();

            foreach($records as $record) {
                $_user = $record->user;
                $params = json_decode($record->params);


                $arr = [
                    0 => $_user->last_name . ' ' . $_user->name,
                    1 => $segments[$record->segment_id],
                    2 => $record->phone,
                    3 => strval($record->day_of_delay),
                    4 => $record->interlocutor,
                    5 => $record->listened_on,
                ];



                foreach ($crits as $key => $crit) {
                    $a = $record->param_values->where('param_id', $crit->id)->first();
                    if($a) {
                        array_push($arr, strval($a->value));
                    } else {
                        array_push($arr, strval(0));
                    }
                }

                array_push($arr, $record->total);
                array_push($arr, $record->comments ? $record->comments : '');

                $data['records'][] = $arr;
            }


        // на локале не работала эта функция, если оставил её на случай откатки
        ob_end_clean();
        if (ob_get_length() > 0) ob_clean();

        return Excel::create('Контроль качества KASPI за '. $request->month.'.'.$request->year, function ($excel) use ($data, $headings, $request) {
            $excel->setTitle('Отчет');
            $excel->setCreator('Laravel Media')->setCompany('MediaSend KZ');
            $excel->setDescription('Экспорт данных в Excel файл');
            $excel->sheet('Отчет за '.$request->day.'.' .$request->month.'.'.$request->year, function ($sheet) use ($data, $headings) {
                $sheet->fromArray($data['records'], null, 'A1', false, false);
                $sheet->prependRow(1, $headings);
            });
        })->export('xls');
    }

    public function changeType(Request $request)
    {
        $group = ProfileGroup::find($request->group_id);

        $group->quality = $request->type;
        $group->save();


        $has_crits = QualityParam::where('group_id', $request->group_id)->first();

        if(!$has_crits) {
            $names = [
                'Установление контакта',
                'Выявление потребностей',
                'Презентация',
                'Работа над возражениями',
                'Закрытие',
            ];

            foreach($names as $name) {
                QualityParam::create([
                    'name' => $name,
                    'group_id' => $request->group_id,
                    'active' => 1,
                ]);
            }
        }
    }

    public function saveCrits(Request $request)
    {
        $group = ProfileGroup::find($request->group_id);

        if($request->can_add_records) {

            foreach ($request->crits as $key => $crit) {
                $param = QualityParam::find($crit['id']);

                if($param) {
                    $param->name = $crit['name'];
                    $param->active = $crit['active'];
                    $param->save();
                } else {
                    QualityParam::create([
                        'name' => $crit['name'],
                        'group_id' => $request->group_id,
                        'active' => $crit['active'],
                    ]);
                }
            }

        } else {


            $dialer = CallibroDialer::where('group_id', $request->group_id)->first();

            if($dialer) {
                $dialer->dialer_id = $request['dialer_id'];
                $dialer->script_id = $request['script_id'] ?? 0;
                $dialer->save();
            } else {
                CallibroDialer::create([
                    'group_id' => $group->id,
                    'dialer_id' => $request['dialer_id'],
                    'script_id' => $request['script_id'] ?? 0
                ]);
            }

        }

        $group->quality = $request->can_add_records ? 'local' : 'ucalls';
        $group->save();
    }

    /**
     * @param int $total
     * @param Request $request
     * @return void
     */
    private function getRank(int $total, Request $request): void
    {
        $rank = 0;
        if ($total >= 20 && $total < 40) {
            $rank = 1;
        } else if ($total >= 40 && $total < 60) {
            $rank = 2;
        } else if ($total >= 60 && $total < 80) {
            $rank = 3;
        } else if ($total >= 80 && $total <= 100) {
            $rank = 4;
        }

        if ($request->is_send_notification) {
            UserNotification::create([
                'user_id' => $request->employee_id,
                'about_id' => 0,
                'title' => 'Оценка переговоров',
                'message' => 'Оценил(а): ' . $request->interlocutor . '<br>Совет: ' . $request->comments . '<br>Общая оценка: ' . $total . ' <div class="Notification-score" data-score="' . $total . '" data-score-rank="' . $rank . '"></div>'
            ]);
        }
    }
}
