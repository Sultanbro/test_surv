<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\Group;
use App\ProfileGroup;
use App\Timetracking;
use App\TimetrackingHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\AnalyticsSettingsIndividually;
use App\Classes\Analytics\Ozon;
use App\Models\Analytics\UserStat;
use App\Imports\UserStatsImport;

class ActivityController extends Controller
{   
    public $date;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function import(Request $request) {
        $user = auth()->user();
        $group_id = $request->group_id;

        if ($request->isMethod('post') && $request->hasFile('file')) {

            // if ($request->file('file')->isValid()) 

            $import = new UserStatsImport;
            Excel::import($import, $request->file('file'));
          
            $headings = $import->headings[0]->toArray(); // first row
            $sheet = $import->data[0]; // first Sheet

            // missing fiels
            //$this->checkMissingFields();

                $table_type = 'minutes'; // минуты

                if(in_array('Менеджер', $headings)) $table_type = 'gatherings';   // сборы  
                if(in_array('среднее время разговора', $headings)) $table_type = 'avg_time';   // ср время разговора      
              
           
                
                // date
                $date_index = 'ДАТА';
                if($group_id == 71){
                    $date_index = 'Дата';
                }
                $excel_date = count($sheet) > 0 && array_key_exists('ДАТА', $sheet[0]) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sheet[0]['ДАТА']) : Carbon::now();
                $date = $excel_date ? $excel_date->format('Y-m-d') : date('Y-m-d');

              
                $items = [];
                $gusers = $this->groupUsers($request->group_id);


                foreach($sheet as $row) {
                    $item = [];
                    //me($row);
                    $item['group_id'] = $request->group_id;
                    $item['activity_id'] = $request->activity_id;

                
                    if($group_id == 42) {

                       
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
                            $item['name'] = $row['Логин'];
                            if($item['name'] == null) continue;
                            $item['date'] = $excel_date ? Carbon::parse($excel_date)->format('Y-m-d') : ''; 
                            $item['data'] = $excel_date ? Carbon::parse($excel_date)->format('d.m.Y') : '';
                            if($item['activity_id'] == 149){
                                $item['hours'] = $this->countHours($row['Время обработки']); 
                            }
                            else{
                                $item['hours'] = $this->countHours($row['Эффективное время']); 
                            }
                            $item['id'] = $this->getPossibleUser($gusers, $item['name']);
                        }   
                        
                        if($table_type == 'avg_time') { 
                            $item['name'] = $row['Логин'];
                            $item['avg_time'] = Carbon::parse($row['Среднее время разговора, сек'])->format('i:s');
                            $item['id'] = $item['name'] ? $this->getPossibleUser($gusers, $item['name']) : 0;
                            if($row['menedzher'] == '') continue;
                        }                     
                        
                    }  
                    
                    array_push($items, $item);
                }


                if($group_id == 42) {
                    
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
                ///me($items);
                return response()->json([
                    'items' => $items,
                    'filename' => '',
                    'users' => $gusers,
                    'date' => $date, 
                    'errors' => []
                ]);
            
        }
    }

    public function saveTimes(Request $request) {

        $date = $request->date;
        foreach($request->items as $item) {
            
            $date = array_key_exists('date', $item) ? $item['date'] : $date;
            $group_id = $item['group_id'];
            
            if($item['id'] != 0) {


                if(!array_key_exists('date', $item)) $item['date'] = $date;
                $this->updateASIs($item);
                

            }

            
            
        }

    }

    

    private function updateASIs(array $item) {


        if($item['group_id'] == 42) {
            $save_value = 0;
            if($item['activity_id'] == 13)  (int)$save_value = $item['gatherings'];
            if($item['activity_id'] == 94) {
                $arr = explode(':', $item['avg_time']);
                $save_value = (int)$arr[0] + ((int)$arr[1] / 60);
                $save_value = round($save_value, 2);
            }
            if($item['activity_id'] == 1)  $save_value = (int)number_format($item['hours'] * 60, 0);

            $this->updateActivity($item, $item['activity_id'], $save_value);
           
        }
        
        if($item['group_id'] == 71) {
            if($item['activity_id'] == 149){
                $save_value = (int)number_format($item['hours'] * 60, 0);
            }else{
                $save_value = round($item['hours'], 1);
            }
            $this->updateActivity($item, $item['activity_id'], $save_value);          
        }

    }

    private function updateActivity($item, $activity_id, $value) {

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
                'value' => round($value,2),
            ]);
        }
    }

    private function getMissingFields(array $array_excel) {
        $missingFields = [
            "fio",
            "data",
            "oplachivaemoe_vremya",
            "vremya_obrabotki",
            "zakryto",
            "rr",
            "sredniy_reyting_mf_autsors"
        ];

        foreach($array_excel as $key => $value) {
            if($value == 'fio') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'data') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'oplachivaemoe_vremya') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'vremya_obrabotki') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'zakryto') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'rr') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'sredniy_reyting_mf_autsors') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }
            
        }
        
        return $missingFields;
    }

 private function countHours($time) {
        if(!str_contains($time,':')){
            $result = round($time * 24,4); 
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

    private function getPossibleUser($users, $fullname) {

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
            dd($fullname);
        }

        
        return $user ? $user->id : 0;
    }

    private function groupUsers($group_id) {
        $users = []; 
        $group = ProfileGroup::find($group_id);

        if($group) {
            $users = json_decode($group->users);
            $users = array_unique($users);
        } 

        $_users = User::whereIn('id', $users)->orderBy('last_name', 'asc')->get(['id', 'last_name', 'name', 'email']);
        return $_users;
    }

    private function checkMissingFields() {

        if(count($missingFields) > 0) {
                
            $str = 'Не хватает полей: ';
            foreach($missingFields as $field) {
                $str .= $field .', ';
            }

            return response()->json([
                'items' => [],
                'filename' => '',
                'users' => [],
                'date' => '',
                'errors' => [$str]
            ]);
        }
    }

}
