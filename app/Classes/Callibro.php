<?php

namespace App\Classes;

use App\User;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class Callibro 
{   
    /**
     * Callibro account of user in jobtron
     */
    public $account;

    public function __construct($email)
    {
        $this->account = DB::connection('callibro')->table('call_account')
            ->where('owner_uid', 5)
            ->where('email', $email)
            ->first();
    }
    /**
     * Call account
     */
    public static function account($email)
    {
        return  DB::connection('callibro')->table('call_account')
            ->where('owner_uid', 5)
            ->where('email', $email)
            ->first();
    }

    /**
     * Call grades for day
     * 
     * @param array $params
     * @param array $script_grades
     * 
     * @return Collection
     */
    public function call_grades(array $params, array $script_grades)
    {
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
     * 
     * @param int $script_id
     * 
     * @return array
     */
    public static function script_grades(int $script_id)
    {
        return DB::connection('callibro')->table('script_grades')
            ->where('script_id', $script_id)
            ->where('status', 1)
            ->select(['id'])
            ->pluck('id')
            ->toArray();
    }


    /**
     * Get worked hours of operator
     * 
     * Sum of ready time, fill time and call time
     * 
     * @param mixed $user_email
     * @param mixed $day
     * 
     * @return [type]
     */
    public static function getWorkedHours($user_email, $day)
    {
        // get account
        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();

        // get bonus seconds for System inaccuracy in timetracking
        $user = User::withTrashed()->where('email', $user_email)->first();
        
        $bonusFactor = $user->full_time == 1 ? 10 : 5; 

        // общее отработанное время
        $workedMinutes = 0; 
        
        if($account) {

            $calls = DB::connection('callibro')->table('calls')
                    ->select('call_account_id', DB::raw('SUM(calls.billsec) as billsec_sum'))
                    ->whereDate('start_time', $day)
                    ->where('billsec', '<', 1000)
                    ->where('call_account_id', $account->id)
                   // ->where('cause', '!=', 'SYSTEM_SHUTDOWN')
                    ->first();
                    
            $reports = DB::connection('callibro')->table('call_account_actions')
                    ->select(
                        DB::raw('SUM(state_duration) as state_duration'),
                        DB::raw('COUNT(state_duration) as count'),
                        'account_id',
                        'operator_status_id',
                        'state'
                    )
                    ->where('account_id', $account->id)
                    //->whereDate('created_at', $day)
                    ->where('created_at', '>=', $day . ' 09:00:00')
                    ->where('created_at', '<=', $day . ' 18:00:00')
                    ->groupBy('state')
                    ->get();

            // count worked seconds
            $ready_sec = 0; 
            $fill_sec = 0; 
            $pause_sec = 0;
            $additional_minutes = 0;

            foreach($reports as $report) {

                if($report->state == 'ready') { 
                    $ready_sec = $report->state_duration;
                }

                if($report->state == 'fill') { 
                    $fill_sec = $report->state_duration;
                }

                if($report->state == 'away') { 
                    $away_sec = $report->state_duration;
                }

                if($report->state == 'pause') { 
                    $pause_sec = $report->state_duration;
                }

                $additional_minutes += $report->count * $bonusFactor;
            }
            
            $workedSeconds = (
                $fill_sec +  // время заполнения анкеты
                $ready_sec + // время в статусе Готов
                $calls->billsec_sum + // время разговора
                $pause_sec + // время в статусе Пауза
                $additional_minutes
            );

            $workedMinutes = (int) round($workedSeconds / 60); // отработанное время в минутах
            
        } 

        return $workedMinutes;
    }

    /**
     * Get start working time of user
     * 
     * @param mixed $user_email
     * @param mixed $day
     * 
     * @return [type]
     */
    public static function startedDay($user_email, $day)
    {

        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();
        
        $full_time = 0; // общее отработанное время
      
        if($account) {
            $call_account_id = $account->id;

    
            $reports = DB::connection('callibro')->table('call_account_actions')
                    ->select(
                        DB::raw('SUM(state_duration) as state_duration'),
                        'account_id',
                        'operator_status_id',
                        'created_at',
                        'state')
                    ->where('account_id', $call_account_id)
                    ->whereDate('created_at', $day)
                    ->orderBy('created_at')->first();

           if($reports) return $reports->created_at;
            
        } 

        return null;
    }

    /**
     * Operator's call minutes 
     * 
     * @param String $user_email
     * @param String $date
     * @param array $params
     * 
     * @return int
     */
    public static function getMinutes(
        String $user_email,
        String $date,
        array $params = []
    ) {
        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();
        
        $full_time = 0; // общее отработанное время

        if($account) {
            
            /**
             * Get billsec_sum = talking seconds
             */
            $calls = DB::connection('callibro')->table('calls')
                    ->select('call_account_id', DB::raw('SUM(calls.billsec) as billsec_sum'))
                    ->whereDate('start_time', $date)
                    ->where('billsec', '>=', 10) // calls from 10 second talking
                    ->where('call_account_id', $account->id)
                    ->where('cause', '!=', 'SYSTEM_SHUTDOWN');

            if(Arr::exists($params, 'dialer_id')) {
                $calls->where('call_dialer_id', $params['dialer_id']);
            }
               
            $calls = $calls->first();

            /**
             * convert to minutes
             */
            $full_time = (int) ceil(($calls->billsec_sum) / 60); // отработанное время в минутах
            
        } 

        return $full_time;
        
    }

    /**
     * Получить корректные согласия по сотруднику
     * 
     * @param String $user_email
     * @param String $date
     * @param array $params
     * 
     * @return int
     */
    public static function getAggrees(
        String $user_email,
        String $date,
        array $params = []
    ) {
        $account = DB::connection('callibro')->table('call_account')->where('email', $user_email)->first();
        
        $aggrees = 0; 
       
        if($account) {

            $aggrees = DB::connection('callibro')->table('calls')
                ->select(
                    'id',
                    DB::raw("DATE_FORMAT(start_time, '%d.%m.%Y %H:%i:%s') as start_time"),
                    'correct_or_not'
                )
                ->whereDate('start_time', $date)
                ->where('correct_or_not', '!=', 2) // незабракованы
                ->where('call_account_id', $account->id);
            
            // диалер
            if(Arr::exists($params, 'dialer_id')) {
                $aggrees->where('call_dialer_id', $params['dialer_id']);
            }
            
            // cкрипты диалера
            if(Arr::exists($params, 'aggrees_scripts')) {
                $aggrees->whereIn('script_status_id', $params['aggrees_scripts']);
            }

            $aggrees = $aggrees->get()->count();
        } 

        return $aggrees;
    }

    /**
     * correct_minutes звонки от 10 секунд
     * 
     * @param String $user_email
     * @param String $date
     * @param array $params
     * 
     * @return int
     */
    public static function getCallCounts(
        String $user_email,
        String $date,
        array $params = []
    ) {
        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();
      
        $calls = 0;

        if($account) {
            $calls = DB::connection('callibro')->table('calls')
                    ->select('id')
                    ->whereDate('start_time', $date)
                    ->where('billsec', '>=', 10) // talking from 10 sec
                    ->where('call_account_id', $account->id)
                    ->where('cause', '!=', 'SYSTEM_SHUTDOWN');
                    
            if(Arr::exists($params, 'dialer_id')) {
                $calls->where('call_dialer_id', $params['dialer_id']);
            }

            $calls = $calls->get()->count();
        } 

        return $calls;
    }

     /**
     * Получить корректные согласия по сотруднику
     * 
     * @param String $user_email
     * @param String $date
     * @param array $params
     * 
     * @return int
     */
    public static function getClosedCards(
        String $user_email,
        String $date,
        array $params = []
    ) {
        $cards = 0; // общее отработанное время

        $cards = DB::connection('callibro')->table('calls')
            ->select(
                'id',
                DB::raw("DATE_FORMAT(start_time, '%d.%m.%Y %H:%i:%s') as start_time")
            )
            ->whereDate('start_time', $date)
            ->where('call_dialer_id', $params['dialer_id'])
            ->whereIn('script_status_id', $params['closed_card_scripts'])
            ->where('cause', '!=', 'NORMAL_TEMPORARY_FAILURE');
        
        if($user_email != '') {
            $account = DB::connection('callibro')->table('call_account')->where('email', $user_email)->first();
            if(!$account) return 0;
            $cards->where('call_account_id', $account->id);
        }
        
        return $cards->get()->count();
    }

}