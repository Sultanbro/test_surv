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

class ActivityController extends Controller
{   
    public $date;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function import(Request $request) {
        $user = User::bitrixUser();
        $uid = $user->ID;

        if ($request->isMethod('post') && $request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                $file = $request->file('file');
                $file_name = $uid.'_'.time().'_'. $file->getClientOriginalName();
                $file->move("files/import/tt/", $file_name);
                $group_id = $request->group_id;

                $file_path = public_path() . '/files/import/tt/' . $file_name;
                $array_excel = [];
                
                $highestColumn = 0;
                
                //dd(get_class_methods('Excel'));
                
                $start_row = 1;
                if($request->group_id == 58) {
                    $start_row = 3;
                }
                config(['excel.import.startRow' => $start_row]);

                //me($file_path);
                $excel = Excel::load($file_path, function ($reader)  {
                    $reader->calculate(false);
                   // me(get_class_methods($reader));
                })->get();

                if(!method_exists($excel, 'getHeading')) { //first shhett
                    $excel = $excel->first();
                } 
               
                $table_type = 'minutes'; // минуты
                if(in_array('menedzher', $excel->getHeading())) {
                    $table_type = 'gatherings';   // сборы      
                } 
                if(in_array('srednee_vremya_razgovora', $excel->getHeading())) {
                    $table_type = 'avg_time';   // ср время разговора      
                } 
                

                if($request->group_id != 42) {
                    $missingFields = $this->getMissingFields($excel->getHeading());

                    if(count($missingFields) > 0) {
                        unlink($file_path);
                        
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
               
                

                if($excel && array_key_exists('data', $excel[0])) {
                    $date = Carbon::parse($excel[0]['data'])->format('Y-m-d');
                } else {
                    $date = date('Y-m-d');
                }

                $items = [];
                $gusers = $this->groupUsers($request->group_id);

                foreach($excel as $row) {
                    //me($row);
                    $item['group_id'] = $request->group_id;
                    $item['activity_id'] = $request->activity_id;

                    if($group_id == 42) {

                       
                        if($table_type == 'minutes') {
                            $item['name'] = $row['fio_sotrudnika'];
                            $item['date'] = $row['data'] ? $row['data']->format('Y-m-d') : ''; 
                            $item['data'] = $row['data'] ? $row['data']->format('d.m.Y') : '';
                            $item['hours'] = $this->countHours($row['minuty']); 
                            $item['id'] = $row['fio_sotrudnika'] ? $this->getPossibleUser($gusers, $row['fio_sotrudnika']) : 0;
                        } else if($table_type == 'gatherings') {
                            $item['name'] = $row['menedzher'];
                            $item['gatherings'] = (int)$row['sborden_v_den'];
                            
                            $item['id'] = $row['menedzher'] ? $this->getPossibleUser($gusers, $row['menedzher']) : 0;
                        } else {
                            $item['name'] = $row['menedzher'];
                            $item['avg_time'] = Carbon::parse($row['srednee_vremya_razgovora'])->format('i:s');
                            $item['id'] = $row['menedzher'] ? $this->getPossibleUser($gusers, $row['menedzher']) : 0;
                        }
                        
                        
                    } else { 
                        $item['name'] = $row['fio'];
                        $item['date'] = $row['data']->format('Y-m-d');
                        $item['data'] = $row['data']->format('d.m.Y');
                        $item['hours'] = $this->countHours($row['oplachivaemoe_vremya']); 
                        $item['process_time'] = $this->countHours($row['vremya_obrabotki']); 
                        $item['tickets'] = (int)$row['zakryto']; 
                        $item['rr'] = number_format((float)$row['rr'] * 100, 2); 
                        $item['avg_rating'] = number_format((float)$row['sredniy_reyting_mf_autsors'], 2);
                        $item['id'] = $row['fio'] ? $this->getPossibleUser($gusers, $row['fio']) : 0;
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
                    'filename' => $file_name,
                    'users' => $gusers,
                    'date' => $date, 
                    'errors' => []
                ]);
            }
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
                'value' => (int)$value,
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

        return $result;
    }

    private function getPossibleUser($users, $fullname) {

        $fullname = explode(' ', $fullname);
        if(count($fullname) >= 2) {
            $last_name = $fullname[0];
            $name = $fullname[1];
        }

        try {
            $user = $users->filter(function ($user) use ($name, $last_name) {
                return $user->NAME == $name || $user->LAST_NAME == $last_name;
            })->first();
        } catch (\Exception $e) {
            dd($name);
        }

        
        return $user ? $user->ID : 0;
    }

    private function groupUsers($group_id) {
        $users = []; 
        $group = ProfileGroup::find($group_id);

        if($group) {
            $users = json_decode($group->users);
            $users = array_unique($users);
        } 

        $_users = User::whereIn('ID', $users)->orderBy('LAST_NAME', 'asc')->get();
        return $_users;
    }



}
