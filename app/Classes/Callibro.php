<?php

namespace App\Classes;

use DB;

class Callibro 
{   
    public $account;

    public function __construct($email) {
        $this->account = DB::connection('callibro')->table('call_account')
            ->where('owner_uid', 5)
            ->where('email', $email)
            ->first();
    }
    /**
     * Call account
     */
    public static function account($email) {
        return  DB::connection('callibro')->table('call_account')
            ->where('owner_uid', 5)
            ->where('email', $email)
            ->first();
    }

    /**
     * Call grades for day
     */
    public function call_grades(array $params, array $script_grades) {
        return DB::connection('callibro')->table('calls')
            ->leftJoin('call_grades as cg', 'cg.call_id', '=', 'calls.id')
            ->select('cg.value', 'cg.call_id')
            ->whereDate('start_time', $params['date'])
            ->where('call_account_id', $this->account->id)
            ->where('call_dialer_id', $params['dialer_id'])
            ->get();
    }  

    /**
     * Id оценок скрипта диалера
     * @return array
     */
    public static function script_grades(int $script_id) {
        return DB::connection('callibro')->table('script_grades')
            ->where('script_id', $script_id)
            ->where('status', 1)
            ->select(['id'])
            ->pluck('id')
            ->toArray();
    }


    public static function getWorkedHours($user_email, $day) {

        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();
        
        $full_time = 0; // общее отработанное время
      
        if($account) {
            $call_account_id = $account->id;

            $ready_sec = 0; // время в статусе Готов
            $fill_sec = 0;  // время заполнения анкеты
            $call_sec = 0;  // время разговора
            
            
            $calls = DB::connection('callibro')->table('calls')
                    ->select('call_account_id', DB::raw('SUM(calls.opersec) as billsec_sum'))
                    ->whereDate('start_time', $day)
                    //->where('billsec', '>=', 10)
                    ->where('call_account_id', $call_account_id)
                    ->where('cause', '!=', 'SYSTEM_SHUTDOWN')
                    ->first();
                    
            $call_sec = $calls->billsec_sum;

            $reports = DB::connection('callibro')->table('call_account_actions')
                    ->select(
                        DB::raw('SUM(state_duration) as state_duration'),
                        'account_id',
                        'operator_status_id',
                        'state')
                    ->where('account_id', $call_account_id)
                    ->whereDate('created_at', $day)
                    ->groupBy('state')->get();

            foreach($reports as $report) {
                if($report->state == 'ready') { 
                  $ready_sec = $report->state_duration;
                }
                if($report->state == 'fill') { 
                  $fill_sec = $report->state_duration;
                }
            }
          
            $full_time = (int) round(($fill_sec + $ready_sec + $call_sec) / 60); // отработанное время в минутах
            
        } 

        return $full_time;
    }

}