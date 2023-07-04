<?php

namespace App\Http\Controllers\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Http\Controllers\Controller;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use Illuminate\Http\Request;
use Auth;
use App\ProfileGroup;
use App\Timetracking;
use App\TimetrackingHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Kpi\Bonus;
use App\Imports\TimetrackingImport;
use App\Service\Department\UserService;

class GroupsController extends Controller
{   
    public function __construct() {
        View::share('title', 'Админ панель');
        View::share('menu', 'timetracking');
        $this->middleware('auth');
    }

    public function saveTimes(Request $request)
    {
        $date = $request->date;
        $group_id = $request->group;
        $additional_minutes_remote = $this->getAdditionalMinutes($date, 'remote', $group_id); // Отсутствие связи доп время для сотрудников Remote
        $additional_minutes_office = $this->getAdditionalMinutes($date, 'inhouse', $group_id); // Отсутствие связи доп время для сотрудников Inhouse

        $edited_users = [];

        foreach($request->items as $item) {
            if($item['id'] == 0) {
                continue;
            }
            
            $record = Timetracking::where('user_id', $item['id'])
                ->whereDate('enter', $date)
                ->orderBy('enter', 'desc')->first();
            
            $user = User::where('position_id', 32)->find($item['id']);
            if(!$user)continue;
            if(!$user->user_type)continue;
            if($user->user_type == 'remote') {
                $additional_minutes = $additional_minutes_remote;
            } else {
                $additional_minutes = $additional_minutes_office;
            }

            array_push($edited_users, $item['id']);

            $minutes = round($item['hours'] * 60);

            if($minutes < 0) $minutes = 0;

            $total_minutes = (int) $minutes + (int) $additional_minutes;

            if($record) {
                $record->total_hours = $total_minutes;
                $record->updated = 1;
                $record->save();
            } else {
                Timetracking::create([
                    'enter' => $date,
                    'exit' => $date,
                    'updated' => 1,
                    'user_id' => $item['id'],
                    'total_hours' => $total_minutes,
                ]);
            }

            $add_text = $additional_minutes != 0 ? ', плюс ' . $additional_minutes . ' минут за отсутствие связи' : '';

            TimetrackingHistory::create([
                'author_id' => Auth::user()->id,
                'author' => Auth::user()->last_name .' '. Auth::user()->name,
                'user_id' => $item['id'],
                'date' => $date,
                'description' => 'Импорт из EXCEL файла: '. $minutes .' минут ('. $request->filename .') '. $add_text
            ]);
        }

        $this->setZeroToUsersStartedTheDay($edited_users, $date, $group_id);
    }

    public function setZeroToUsersStartedTheDay($user_ids, $date, $group_id)
    {

        $group = ProfileGroup::find($group_id); 
        
        $users = array_diff(json_decode($group->users, true), $user_ids);
        
        $users = array_values($users);
        
        $users = User::with('user_description')
                ->whereHas('user_description', function ($query) {
                    $query->where('is_trainee', 0);
                })
                ->whereIn('id', $users)->where('position_id', 32)->get('id')->pluck('id')->toArray();
      
        $tts = Timetracking::whereDate('enter', $date)
                ->whereIn('user_id', $users)
                ->get();
             
            foreach ($tts as $tt) {
                $tt->total_hours = 0;
                $tt->updated = 1;
                $tt->save();

                TimetrackingHistory::create([
                    'author_id' => Auth::user()->id,
                    'author' => Auth::user()->last_name .' '. Auth::user()->name,
                    'user_id' => $tt->user_id,
                    'date' => $date,
                    'description' => 'Импорт из EXCEL файла: 0 минут'
                ]);
            }

    }

    public function getAdditionalMinutes($date, $type, $group_id)
    {

        $day = Carbon::parse($date)->day;
        $column = AnalyticColumn::where('group_id', $group_id)
            ->where('date', Carbon::parse($date)->day(1)->format('Y-m-d'))
            ->where('name', $day)
            ->first();

        if(!$column) return 0;

        $stat = AnalyticStat::where('type', $type)
            ->where('column_id', $column->id)
            ->where('date', Carbon::parse($date)->day(1)->format('Y-m-d'))
            ->first();
        
        return $stat ? $stat->value : 0;
    }

    /**
     * @TODO исправить для всех кабинетов
     * ЭТОТ МЕТОД ТОЛЬКО РАБОТАЕТ в BP.jobtron.org
     */
    public function import(Request $request)
    {
        $user = auth()->user();
        $group_id = $request->group_id;
        

        if ($request->isMethod('post') && $request->hasFile('file')) {

            $import = new TimetrackingImport;
            Excel::import($import, $request->file('file'));
            
            $headings = $import->headings; // first row
            $sheet = $import->data[0]; // first Sheet

            $date_field = 'Дата и время создания';
            
            $excel_date = count($sheet) > 0 ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet[0][$date_field]) : Carbon::now();
            $date = $excel_date ? $excel_date->format('Y-m-d') : date('Y-m-d');

            foreach($sheet as $key => $item) {
                $sheet[$key]['fullname'] = $item['Фамилия сотрудника'] . ' ' . $item['Имя сотрудника'];
            }

            $usernames = [];
            foreach($sheet as $element) {
                $usernames[$element['fullname']][] = $element;
            }

            if($group_id == 42) {
                $gusers = $this->groupUsers(42);
                $gusers = collect($gusers)->sortBy('name');
            }

            if($group_id == 88) {
                $gusers = $this->groupUsers(88);
                $gusers = collect($gusers)->sortBy('name');
            }
            
            $items = [];
            
            foreach($usernames as $username => $values) {
                $item = [];
                $item['name'] = $username;
                $item['id'] = 0;
                $item['dinner'] = true;

                foreach($values as $val) {
                    if(is_null($val[$date_field])) continue;
                
                    try {
                        $val[$date_field] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[$date_field])->getTimestamp(); 
                    } catch(\Exception $e) {
                        continue;
                    }
                }

                $values = collect($values)->sortBy($date_field);
                if($_fv = $values->first()) {
                    $possible_user = $gusers->where('name' , $_fv['Имя сотрудника'])->where('last_name',$_fv['Фамилия сотрудника'])->first();
                    
                    if($possible_user) {
                        $item['id'] = $possible_user->id ?? $possible_user['id'];
                    }
                }
                
                $earliest = 9999999999;
                $latest = 0;
                $hours = 0;
                $last_date = null;
                $last_duration = 0;
                
                foreach($values as $val) {
                    if(is_null($val[$date_field])) continue;
                    $ts = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[$date_field])->getTimestamp();

                    try {
                        $_duration = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val['Длительность разговора'])->getTimestamp();
                        $duration = date('H', $_duration) * 3600 + date('i', $_duration) * 60 + date('s', $_duration);
                    } catch(\Exception $e) {
                        $duration = 0;
                    }
                    

                    if($last_date) {
                        
                        if($ts - $last_date - $last_duration > 930){ // Разница больше 15 минут без разговоров
                            $hours +=  $latest - $earliest + $last_duration;
                            
                            $earliest = $ts;
                            $latest = $ts;
                        }
                    }


                    $last_date = $ts;
                    $last_duration = $duration;

                    if($ts < $earliest) $earliest = $ts;
                    if($ts > $latest) $latest = $ts; 
                }  

                $hours +=  $latest - $earliest;
        
                $diff = number_format($hours / 3600, 1);

                if($diff > 11) $diff = 11;
                $item['hours'] = $diff;
                
                array_push($items, $item);
            }
                
            return response()->json([
                'items' => $items,
                'filename' => '',
                'users' => $gusers->values()->all(),
                'date' => $date,
                'errors' => []
            ]);
        }
    }

    private function groupUsers($group_id) : array
    {
        return (new UserService)->getEmployees($group_id, date('Y-m-d'));
    }

    public function saveKaspiHours($user_id, $minutes, $date) 
    {
        $date = Carbon::parse($date);


        $us = UserStat::where('date', $date->format('Y-m-d'))
            ->where('user_id', $user_id)
            ->where('activity_id', 1)
            ->first();

        if($us) {
            $us->value = $minutes;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $user_id,
                'activity_id' => 1,
                'value' => minutes,
            ]);
        }
        
    }

    public function saveBonuses(Request $request) 
    {
        $group_id = 0;
        foreach($request->bonuses as $_bonus) {
            $group_id = $_bonus['group_id'];

            if($_bonus['id'] == 0) {
                unset($_bonus['id']);
                Bonus::create((array)$_bonus);
            } else {
                $bonus = Bonus::find($_bonus['id']);
                unset($_bonus['id']);
                if($bonus) {
                    $bonus->update((array)$_bonus);
                } else {
                    Bonus::create((array)$_bonus);
                }
            }
            
        }
        
        return json_encode([
            'bonuses' => Bonus::where('group_id', $group_id)->get()
        ]);
    }
}
