<?php

namespace App\Http\Controllers;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\ProfileGroupUser;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Group;
use App\ProfileGroup;
use App\Timetracking;
use App\TimetrackingHistory;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Admin\Bonus;

class GroupsController extends Controller
{   
    public function __construct() {
        View::share('title', 'Админ панель');
        View::share('menu', 'timetracking');
        $this->middleware('auth');
    }

    public function saveTimes(Request $request) {
        
        $date = $request->date;
        $additional_minutes_remote = $this->getAdditionalMinutes($date, 'remote'); // Отсутствие связи доп время для сотрудников Remote
        $additional_minutes_office = $this->getAdditionalMinutes($date, 'inhouse'); // Отсутствие связи доп время для сотрудников Inhouse

        $edited_users = [];
        foreach($request->items as $item) {
            if($item['id'] != 0) {

                array_push($edited_users, $item['id']);
                $record = Timetracking::where('user_id', $item['id'])
                    ->whereDate('enter', $date)
                    ->orderBy('enter', 'desc')->first();
                
                $user = User::find($item['id']);
                if(!$user)continue;
                if(!$user->user_type)continue;
                if($user->user_type == 'remote') {
                    $additional_minutes = $additional_minutes_remote;
                } else {
                    $additional_minutes = $additional_minutes_office;
                }

                $minutes = round($item['hours'] * 60);

                if($minutes < 0) $minutes = 0;

                if($record) {
                    $record->total_hours = $minutes + $additional_minutes;
                    $record->updated = 1;
                    $record->save();
                } else {
                    Timetracking::create([
                        'enter' => $date,
                        'exit' => $date,
                        'updated' => 1,
                        'user_id' => $item['id'],
                        'total_hours' => $minutes + $additional_minutes,
                    ]);
                }

                //$this->saveKaspiHours($item['id'], $minutes, $date);

                $add_text = $additional_minutes != 0 ? ', плюс ' . $additional_minutes . ' минут за отсутствие связи' : '';

                TimetrackingHistory::create([
                    'author_id' => Auth::user()->ID,
                    'author' => Auth::user()->LAST_NAME .' '. Auth::user()->NAME,
                    'user_id' => $item['id'],
                    'date' => $date,
                    'description' => 'Импорт из EXCEL файла: '. $minutes .' минут ('. $request->filename .') '. $add_text
                ]);
            }
            
        }


        $this->setZeroToUsersStartedTheDay($edited_users, $date);
    }


    public function setZeroToUsersStartedTheDay($user_ids, $date) {

        $pgu = ProfileGroupUser::where([
            'group_id' => 42,
            'date' => $date,
        ])->first();

        if($pgu) {
            $tts = Timetracking::whereDate('enter', $date)
                ->where('group_id', 42)
                ->whereNotIn('user_id', $user_ids)
                ->get();

            foreach ($tts as $tt) {
                $tt->minutes = 0;
                $tt->updated = 1;
                $tt->save();
            }
        }

    }

    public function getAdditionalMinutes($date, $type) {

        $day = Carbon::parse($date)->day;
        $column = AnalyticColumn::where('group_id', 42)
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
                $excel = Excel::load($file_path, function ($reader)  {
                    $reader->calculate(false);
                })->get();

                if(!method_exists($excel, 'getHeading')) { //first shhett
                    $excel = $excel->first();
                } 

                $heading = $excel->getHeading();

                $missingFields = $this->getMissingFields($heading);

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

                
                
                if($excel) {
                    $date = Carbon::parse($excel[0]['data_i_vremya_sozdaniya'])->format('Y-m-d');
                    
                } else {
                    $date = date('Y-m-d');
                }

                foreach($excel as $item) {
                    
                    $item->fullname = $item['familiya_sotrudnika'] . ' ' . $item['imya_sotrudnika'];
                }

                $usernames = $excel->groupBy('fullname');
                
                if($request->group_id == 35 || $request->group_id == 42) {
                    $gusers = $this->groupUsers(35);
                    $gusers2 = $this->groupUsers(42);
                    $gusers = $gusers->merge($gusers2);
                    $gusers = $gusers->sortBy('NAME');
                }
                
                $items = [];
                
                foreach($usernames as $username => $values) {
                    $item = [];
                    $item['name'] = $username;
                    $item['id'] = 0;
                    $item['dinner'] = true;
                    $values = $values->sortBy('data_i_vremya_sozdaniya');

                    $_fv = $values->first();
                    
                    if($_fv) {
                        
                        $possible_user = $gusers->where('NAME' , $_fv['imya_sotrudnika'])->where('LAST_NAME',$_fv['familiya_sotrudnika'])->first();
                        
                        if($possible_user) {
                            $item['id'] = $possible_user->ID;
                        }
                    }
                    
                    
                    //

                    $earliest = 9999999999;
                    $latest = 0;


                    $hours = 0;
                    $last_date = null;
                    $last_duration = 0;
                    
                    foreach($values as $val) {
                        if(is_null($val['data_i_vremya_sozdaniya'])) continue;
                        //if(!property_exists($val['data_i_vremya_sozdaniya'], timestamp)) continue;
                        
                        try {
                            $ts = $val['data_i_vremya_sozdaniya']->timestamp; 
                        } catch(\Exception $e) {
                            continue;
                        }


                        try {
                            $_duration = $val['dlitelnost_razgovora']->timestamp;
                            $duration = date('H', $_duration) * 3600 + date('i', $_duration) * 60 + date('s', $_duration);
                        } catch(\Exception $e) {
                            $duration = 0;
                        }
                        

                        if($last_date) {

                            if($ts - $last_date - $last_duration > 930){ // Разница больше 15 минут без разговоров
                                $hours +=  $latest - $earliest + $last_duration;
                                

                                //if($item['id'] == 4184) dump($hours / 3600);
                                //if($item['id'] == 4184) dump(Carbon::createFromTimestamp($earliest)->format('H:i:s'));
                                //if($item['id'] == 4184) dump(Carbon::createFromTimestamp($latest)->format('H:i:s'));

                                $earliest = $ts;
                                $latest = $ts;
                            // dump('ts '. ($ts)); 
                            // dump('last_date '. ($last_date)); 
                            // dump('duration '. ($last_duration)); 
                            // dump('вввв '. ($ts - $last_date - $last_duration)); 
                            //dump('earliest '. date('Y-m-d H:i:s', $earliest)); 
                                
                              // dump('ts '. date('Y-m-d H:i:s', $ts));
                                
                              //  dump('last_date '. date('Y-m-d H:i:s', $last_date));
                              // dump('hours '. $hours);
                            }
                        }

                        $last_date = $ts;
                        $last_duration = $duration;

                        if($ts < $earliest) $earliest = $ts;
                        if($ts > $latest) $latest = $ts; 
                    }  

                   // if($item['id'] == 4184) dump($hours / 3600);
                   // if($item['id'] == 4184) dump(Carbon::createFromTimestamp($earliest)->format('H:i:s'));
                   // if($item['id'] == 4184) dump(Carbon::createFromTimestamp($latest)->format('H:i:s'));
                    $hours +=  $latest - $earliest;
                   // if($item['id'] == 4184) dump($hours / 3600);
                    $diff = number_format($hours / 3600, 1);

                    if($diff > 11) $diff = 11;
                    $item['hours'] = $diff;
                   

                    array_push($items, $item);
                }
                    
                return response()->json([
                    'items' => $items,
                    'filename' => $file_name,
                    'users' => $gusers->values()->all(),
                    'date' => $date,
                    'errors' => []
                ]);
            }
        }
    }

    private function getMissingFields(array $array_excel) {
        $missingFields = [
            "imya_sotrudnika",
            "familiya_sotrudnika",
            "dlitelnost_razgovora",
            "data_i_vremya_sozdaniya"
        ];

        foreach($array_excel as $key => $value) {
            if($value == 'familiya_sotrudnika') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'imya_sotrudnika') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'dlitelnost_razgovora') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }

            if($value == 'data_i_vremya_sozdaniya') {
                if (($keyx = array_search($value, $missingFields)) !== false) {
                    unset($missingFields[$keyx]);
                }
            }
            
        }
        
        return $missingFields;
    }

    private function groupUsers($group_id) {
        $users = []; 
        $groups = ProfileGroup::where('id', $group_id)->get();

     

        
        foreach($groups as $group) {
            $gr = $group->groupUsers();
            if($gr) {
                foreach($gr as $g) {
                    array_push($users, $g->ID); 
                }
            }   
        }

    
        $users = array_unique($users);

        $_users = User::whereIn('ID', $users)->orderBy('LAST_NAME', 'asc')->get();
        return $_users;
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

    /**
     * Сохранить настройки бонусов группы
     */
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
