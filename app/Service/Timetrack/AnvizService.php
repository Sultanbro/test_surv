<?php

namespace App\Service\Timetrack;

use App\Timetracking;
use App\Models\Anviz\Time as AnvizTime;
use Carbon\Carbon;

class AnvizService
{   
    public $date;

    public function __construct()
    {

    }

    public function fetchMarkTimes($date = null) : void
    {   
        $this->date = $date ?? date('Y-m-d');

        $timeToFetchPreviousDays = date('w', strtotime($date)) == 1
                                && date("h:i") == '07:00';

        $dates = $timeToFetchPreviousDays
               ? [date('Y-m-d'), date('Y-m-d', strtotime(' -1 day')), date('Y-m-d', strtotime(' -2 day'))]
               : [$this->date];

        foreach ($dates as $my_date) {
            $this->date = $my_date;
            $this->resolveOneDay();
        } 
    }

    private function resolveOneDay() : void
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

//            /**
//             * If user already has Timetracking records for date
//             */
//            $exit_null = $user_records->where('exit', null)
//                ->sortByDesc('enter')
//                ->first();
//
//            if($exit_null) {
//                $this->updateIfDiffMoreThan15Mins($last_anviz_date, $exit_null);
//            }

            /**
             * If user not has Timetracking record for date
             */
            if($user_records->isEmpty()) {
                Timetracking::create([
                    'enter'   => Carbon::parse($last_anviz_date)->subHours(6),
                    'user_id' => $user_id
                ]);
            }

        } 
    }

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

    private function anvizRecords()
    {
        return AnvizTime::orderBy('CheckTime', 'desc')
                    ->whereDate('CheckTime', $this->date)
                    ->get();
    }

    private function getUserIds($records = null) : array
    {
        $users_array = [];
        foreach($records->unique('Userid') as $record) {
            $users_array[] = $record->Userid;
        }

        return $users_array; // [1,2,3]
    }
    
    private function timetrackingRecords(array $users)
    {
        return Timetracking::whereDate('enter', $this->date)
                ->whereIn('user_id', $users)
                ->get(); 
    }

}