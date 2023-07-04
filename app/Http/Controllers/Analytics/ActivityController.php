<?php

namespace App\Http\Controllers\Analytics;

use Illuminate\Http\Request;
use App\Timetracking;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\Analytics\UserStat;
use App\Imports\UserStatsImport;
use App\Service\Department\UserService;

class ActivityController extends Controller
{   
    public $date;

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * @TODO исправить для всех кабинетов
     * ЭТОТ МЕТОД ТОЛЬКО РАБОТАЕТ в BP.jobtron.org
     */
    public function import(Request $request)
    {

        $group_id = $request->group_id;

        if ($request->isMethod('post') && $request->hasFile('file')) {

            $import = new UserStatsImport;
            Excel::import($import, $request->file('file'));
          
            $headings = $import->headings[0]->toArray(); // first row
            $sheet = $import->data[0]; // first Sheet

            $table_type = 'minutes'; // минуты
    
            if(in_array('Менеджер', $headings)) $table_type = 'gatherings';   // сборы  
            if(in_array('среднее время разговора', $headings)) $table_type = 'avg_time';   // ср время разговора      
            
            $excel_date = count($sheet) > 0 && array_key_exists('ДАТА', $sheet[0]) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet[0]['ДАТА']) : Carbon::now();
            $date = $excel_date ? $excel_date->format('Y-m-d') : date('Y-m-d');

            $items = [];
            $gusers = $this->groupUsers($request->group_id);

            foreach($sheet as $row) {
                $item = [];
        
                $item['group_id'] = $request->group_id;
                $item['activity_id'] = $request->activity_id;

                if($group_id == 42 || $group_id == 88) { //Kaspi


                    if($table_type == 'minutes') {
                        $item['name'] = $row['ФИО сотрудника'];
                        if($item['name'] == null) continue;
                        $excel_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['ДАТА']);
                        $item['date'] = $excel_date ? Carbon::parse($excel_date)->format('Y-m-d') : ''; 
                        $item['data'] = $excel_date ? Carbon::parse($excel_date)->format('d.m.Y') : '';
                        $item['hours'] = $this->countHours($row['минуты']); 
                        $item['id'] = $this->getPossibleUser($gusers, $item['name']);
                    } 

                    if($table_type == 'gatherings') {
                        $item['name'] = $row['Менеджер'];
                        $item['gatherings'] = (int)$row['Сбор(день в день)'];
                        
                        $item['id'] = $item['name'] ? $this->getPossibleUser($gusers, $item['name']) : 0;
                    } 
                    
                    if($table_type == 'avg_time') { 
                        $item['name'] = $row['Менеджер'];
                        $item['avg_time'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['среднее время разговора'])->format('i:s');
                        $item['id'] = $item['name'] ? $this->getPossibleUser($gusers, $item['name']) : 0;
                        if($row['Менеджер'] == '') continue;
                    }
                    
                    
                }

                if($group_id == 71) {
                    if($table_type == 'minutes') {
                        if($item['activity_id'] == 149)
                            $item['name'] = $row['Имя оператора'];
                        else
                            $item['name'] = $row['Логин'];
                        if($item['name'] == null) continue;
                        $excel_date = array_key_exists('Дата', $row) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Дата']) : Carbon::now();
                        $item['date'] = $excel_date ? Carbon::parse($excel_date)->format('Y-m-d') : ''; 
                        $item['data'] = $excel_date ? Carbon::parse($excel_date)->format('d.m.Y') : '';
                        if($item['activity_id'] == 149){
                            $item['hours'] = round($this->countHours($row['Суммарное время в режиме разговора']) * 60 ,1); 
                        }
                        else if($item['activity_id'] == 151){
                            $item['hours'] = $this->countHours($row['Эффективное время']); 
                        }
                        $item['id'] = $this->getPossibleUser($gusers, $item['name']);
                    }   


                    /*if($table_type == 'avg_time') { 
                        $item['name'] = $row['Имя оператора'];
                        $item['avg_time'] = Carbon::parse($row['Эффективное время'])->format('i:s');
                        $item['id'] = $item['name'] ? $this->getPossibleUser($gusers, $item['name']) : 0;
                        if($row['menedzher'] == '') continue;
                    }   */ 
                }  
                
                array_push($items, $item);
            }

            if($group_id == 42  || $group_id == 88) {
                
                if($table_type == 'minutes') { 

                    $sorted_users = collect([]);
                    $users = [];
                    foreach($items as $item) {
                        $name = $item['name'];
                        $date = $item['date'];
                        $hours = $item['hours'];

                        if(!in_array($name, $users)) {
                            array_push($users, $name);
                            $sorted_users->push(collect($item));
                        } else {
                            $on_date = $sorted_users->where('date', $date)->where('name', $name);
                            
                            if($on_date->count() > 0) {
                                foreach($on_date as $key => $ondate) {
                                    $ondate['hours'] = $ondate['hours'] + $hours;
                                }
                            } else {
                                $sorted_users->push($item); 
                            }
                        }
                            
                        
                    }

                    $items = $sorted_users->toArray();

                } 
            } 
            
            return response()->json([
                'items' => $items,
                'filename' => '',
                'users' => $gusers,
                'date' => $date, 
                'errors' => []
            ]);
            
        }
    }

    public function saveTimes(Request $request)
    {
        $date = $request->date;
        foreach($request->items as $item) {
            if($item['group_id'] == 71){
                $item['date'] = $date;
            } else{
                $date = array_key_exists('date', $item) ? $item['date'] : $date;
            }
            
            if($item['id'] != 0) {
                if(!array_key_exists('date', $item)) $item['date'] = $date;
                $this->updateASIs($item);
            }       
        }
    }

    private function updateASIs(array $item)
    {
        if($item['group_id'] == 42) {
            $save_value = 0;
            if($item['activity_id'] == 13)  (int)$save_value = $item['gatherings'];
            if($item['activity_id'] == 94) {
                $arr = explode(':', array_key_exists('avg_time', $item) ? $item['avg_time'] : '00:00:00');
                $save_value = (int)$arr[0] + ((int)$arr[1] / 60);
                $save_value = round($save_value, 2);
            }
            if($item['activity_id'] == 1)  $save_value = (int)number_format($item['hours'] * 60, 0);

            $this->updateActivity($item, $item['activity_id'], $save_value);
           
        }

        if($item['group_id'] == 88) {
            $save_value = 0;
            if($item['activity_id'] == 165) {
                $arr = explode(':', array_key_exists('avg_time', $item) ? $item['avg_time'] : '00:00:00');
                $save_value = (int)$arr[0] + ((int)$arr[1] / 60);
                $save_value = round($save_value, 2);
            }
            if($item['activity_id'] == 164)  $save_value = (int)number_format($item['hours'] * 60, 0);

            $this->updateActivity($item, $item['activity_id'], $save_value);
           
        }
        
        if($item['group_id'] == 71) {
            if($item['activity_id'] == 149){
                $save_value = (int)number_format($item['hours'], 0);
            }else{
                $save_value = round($item['hours'], 1);
            }
            $this->updateActivity($item, $item['activity_id'], $save_value);          
        }
    
    }

    private function updateActivity($item, $activity_id, $value)
    {
        $date = Carbon::parse($item['date'])->format('Y-m-d');

        $us = UserStat::where('date', $date)
            ->where('user_id', $item['id'])
            ->where('activity_id', $activity_id)
            ->first();

        if($us) {
            $us->value = $value;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $item['id'],
                'activity_id' => $activity_id,
                'value' => $value,
            ]);
        }

        if($value > 0 && in_array($activity_id,[151]) ){
            Timetracking::updateTimes($item['id'], $date, $value * 60);
        }
    }

    private function countHours($time)
    {
        if(!str_contains($time,':')){
            $result = round($time * 24,1); 
        }
        else{
            $time = explode(':', $time);
            if(count($time) == 3) {
                $hours = (int)$time[0];
                $minutes = (int)$time[1];
                $seconds = (int)$time[2];

                $result = $hours + $minutes / 60 + $seconds / 3600;
                $result = number_format($result, 4);
            } else {
                $result = 0;
            }
        }

        return $result;
    }

    private function getPossibleUser($users, $fullname)
    {

        $fullname = explode(' ', $fullname);

        $name = '';
        $last_name = '';
        if(count($fullname) >= 2) {
            $last_name = $fullname[0];
            $name = $fullname[1];
        }
        if(count($fullname) == 1) {
            $last_name = $fullname[0];
        }

        try {
            $user = $users->filter(function ($user) use ($name, $last_name) {
                return $user->name == $name || $user->last_name == $last_name;
            })->first();
        } catch (\Exception $e) {
            return $e;
        }

        
        return $user ? $user->id : 0;
    }

    private function groupUsers($group_id)
    {
        return collect((new UserService)->getEmployees($group_id, date('Y-m-d')));
    }
}
