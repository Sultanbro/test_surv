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

    /**
     * set date before fetch
     * 
     * @param mixed $date
     * @return void
     */
    private function setDateBeforeFetch($date) : void
    {
        if(is_null($date)) {
            $this->date = date('Y-m-d');
        } else {
            $this->date = $date;
        }
    }

    /**
     * Main function
     * 
     * @param mixed $date
     * @return void
     */
    public function checkInOut($date = null) : void
    {   
        $this->setDateBeforeFetch($date);

        $timeToFetchPreviousDays = date('w', strtotime($date)) == 1
                                && date("h:i") == '07:00';

        $dates = $timeToFetchPreviousDays
               ? [date('Y-m-d'), date('Y-m-d', strtotime(' -1 day')), date('Y-m-d', strtotime(' -2 day'))]
               : [$this->date];

        foreach ($dates as $my_date) {
            $this->date = $my_date;
            $this->resolveDay();
        } 

    }
    
    
    /**
     * resolve One Day
     */
    private function resolveDay() : void
    {
        $anvizRecords        = $this->anvizRecords();
        $usersIds            = $this->getUserIds($anvizRecords);
        $timetrackingRecords = $this->timetrackingRecords($usersIds);
        
        foreach($usersIds as $user_id) {
           
            $user_records = $timetrackingRecords->where('user_id', $user_id);

            $last_anviz_date = $anvizRecords->where('Userid', $user_id)
                                        ->sortByDesc('CheckTime')
                                        ->first()
                                        ->CheckTime;
            /**
             * If user not has Timetracking record for date
             */
            if($user_records->isEmpty()) {

                Timetracking::create([
                    'enter'   => $last_anviz_date,
                    'user_id' => $user_id
                ]);

                continue;
            }

            /**
             * If user already has Timetracking records for date
             */
            $exit_null = $user_records->where('exit', null)
                                    ->sortByDesc('enter')
                                    ->first();

            if($exit_null) {
                $this->updateIfDiffMoreThan15Mins($last_anviz_date, $exit_null);
            } 

        } 
    }

    /**
     * 
     */
    private function updateIfDiffMoreThan15Mins($anvizDate, Timetracking $timetracking) : void
    {
        $diffMore15Minutes = strtotime($anvizDate)
                        - strtotime($timetracking->enter)
                        - 900;

        if($diffMore15Minutes > 0) {
            $timetracking
                ->update([
                    'exit' => $anvizDate
                ]);
        }
    }

    /**
     * @return Collection
     */
    private function anvizRecords()
    {
        return AnvizTime::orderBy('CheckTime', 'desc')
                    ->whereDate('CheckTime', $this->date)
                    ->get(); 
    }

    /**
     * get user ids in array
     * 
     * @return array
     */
    private function getUserIds($records = null) : array
    {
        $users_array = [];
        foreach($records->unique('Userid') as $record) {
            array_push($users_array, $record->Userid);
        }

        return $users_array; // [1,2,3]
    }
    
    /**
     * @return Collection
     */
    private function timetrackingRecords(array $users)
    {
        return Timetracking::whereDate('enter', $this->date)
                ->whereIn('user_id', $users)
                ->get(); 
    }

}
