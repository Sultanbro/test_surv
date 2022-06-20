<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Components\TelegramBot;
use App\Http\Controllers\Controller;
use App\TimetrackingHistory;
use App\ProfileGroup;
use App\Setting;
use App\Timetracking;
use App\User;
use App\Models\Anviz\AnvizUser;
use App\Models\Anviz\Time as AnvizTime;
use App\Models\Anviz\DBAnviz;

class AnvizController extends Controller
{   
    public $date;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkInOut($date = null)
    {   
        if(is_null($date)) {
            $this->date = date('Y-m-d');
        } else {
            $this->date = $date;
        }

        $todays_records = $this->todaysRecords();

       
    //    dd($todays_records->where('Userid', 14476)->first());
        $users_array = $this->getUsersArray($todays_records);
        $todays_timetracking_records = $this->todaysTimetrackingRecords($users_array);

        foreach($users_array as $user_id) {
            
            $current_user_timetracking_records = $todays_timetracking_records->where('user_id', $user_id);
            
            $last_anviz_date = $todays_records->where('Userid', $user_id)->sortByDesc('CheckTime')->first()->CheckTime;

            if($current_user_timetracking_records->isNotEmpty()) {

                $exit_is_null_records = $current_user_timetracking_records->where('exit', null);
                if($exit_is_null_records->isNotEmpty()) {

                    $last_timetracking_date = $exit_is_null_records->sortByDesc('enter')->first()->enter;
                    
                    $difference = strtotime($last_anviz_date) - strtotime($last_timetracking_date) - 900;
                    if($difference > 0) {
                        $exit_is_null_records->sortByDesc('enter')->first()->update(['exit' => $last_anviz_date]);
                    }

                } else {

                    $last_timetracking_date = $current_user_timetracking_records->sortByDesc('exit')->first()->exit;
                    
                    $difference = strtotime($last_anviz_date) - strtotime($last_timetracking_date);
                    if($difference != 0 && $difference > 0) {
                        Timetracking::create(['enter' => $last_anviz_date, 'user_id' => $user_id]);
                    }
                    
                }   
            } else { 
                Timetracking::create(['enter' => $last_anviz_date, 'user_id' => $user_id]);
            }
        }

        //$times = Timetracking::where('user_id', $user_id)->whereDate('CheckTime', now())->get();



    }

    private function todaysRecords() {
        // date('Y-m-d H:i:s', strtotime('+1 day', time()))
        return AnvizTime::orderBy('CheckTime', 'desc')->whereDate('CheckTime', $this->date)->get(); 
        //return AnvizTime::orderBy('CheckTime', 'desc')->whereDate('CheckTime', date('Y-m-d', strtotime('-2 day', time())))->get(); 
    }

    private function getUsersArray($records = null) {
        $users_array = [];
        foreach($records->unique('Userid') as $record) {
            array_push($users_array, $record->Userid);
        }
        return $users_array; // [1,2,3]
    }
 
    private function todaysTimetrackingRecords(array $users) {
        // dd(Timetracking::whereDate('enter', date('Y-m-d', time() - 3600 * 24 * 2))->get());
        // dd(Timetracking::whereDate('enter', date('Y-m-d', strtotime('-2 day', time())))->whereIn('user_id', $users)->get());
        return Timetracking::whereDate('enter', $this->date)->whereIn('user_id', $users)->get(); 
    }

}
