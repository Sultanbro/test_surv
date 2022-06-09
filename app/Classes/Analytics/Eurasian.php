<?php
namespace App\Classes\Analytics;

use Auth;
use App\User;
use Carbon\Carbon;
use App\Trainee;
use App\Timetracking;
use App\TimetrackingHistory;
use App\DayType;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually as ASI;
use DB;

class Eurasian 
{
    public $table1 = 'analytics_settings';
    public $table2 = 'analytics_settings_individually';

    /**
     * Группа Евразийский банк
     */
    CONST ID = 53;
    CONST GROUP_ID = [53];
    CONST ACTIVITIES = [
        '53' => [
            16, // Минуты
            17, // Оценка диалога
            18, // Согласия
        ],
    ];

    /**
     * Поля сводной таблицы
     */
    CONST S_TOTAL = 0; // первая пустая строка
    CONST S_IMPL = 1; // impl
    CONST S_PRCSTLL = 2; // Pr, cstll
    CONST S_AGGREES = 3; // Факт 
    CONST S_PLAN = 4; // Plan 
    CONST S_AVG_CONVERSION = 5; // Средняя конверсия
    CONST S_MINUTES = 6; // Минуты штатных
    CONST S_LEADS = 7; // Поступление лидов
    CONST S_OPERATORS_PLAN = 8; // План операторов на линии
    CONST S_OPERATORS = 9; // Кол-во менеджеров
    CONST S_INCORRECT_DIALOGS = 10; // Кол-во некоррект диалогов
    CONST S_LOST_CALLS = 11; // Потерянные звонки
    CONST S_CLOSED_CARDS = 12; // Закрыто карт

    
    public static function defaultSummaryTable() {
        return [
            ['rownumber' => 2,'headers' => 0, 'pr' => 0,'plan' => 0],
            ['rownumber' => 3,'headers' => 'Impl', 'pr' => 0,'plan' => 0],
            ['rownumber' => 4,'headers' => 'Pr, cstll', 'pr' => 0,'plan' => 0],
            ['rownumber' => 5,'headers' => 'Факт согласий', 'pr' => 0,'avg' => 0],
            ['rownumber' => 6,'headers' => 'Plan согласий', 'pr' => 0],
            ['rownumber' => 7,'headers' => 'Средняя конверсия', 'avg' => 0],
            ['rownumber' => 8,'headers' => 'Минуты штатных', 'pr' => 0,'avg' => 0],
            ['rownumber' => 9,'headers' => 'Поступление лидов', 'pr' => 0,],
            ['rownumber' => 10,'headers' => 'План операторов', 'pr' => 0],
            ['rownumber' => 11,'headers' => 'Кол-во операторов', 'pr' => 0],
            ['rownumber' => 12,'headers' => 'Кол-во некоррект диалогов', 'pr' => 0],
            ['rownumber' => 13,'headers' => 'Потерянные звонки', 'pr' => 0],
            ['rownumber' => 14,'headers' => 'Закрыто карт', 'pr' => 0,'avg' => 0],
        ];
    }

    public static function getTotals($group_id, Carbon $date) {
        $arr = [
            self::S_MINUTES => [],
            self::S_OPERATORS => [],
            self::S_AGGREES => [],
        ];

        $asis = [
            self::S_MINUTES =>  ASI::where([
                    'date' => $date->startOfMonth()->format('Y-m-d'),
                    'group_id' => $group_id,
                    'type' => self::ACTIVITIES[$group_id][0], // минуты
                ])->get(),
            self::S_AGGREES =>  ASI::where([
                    'date' => $date->startOfMonth()->format('Y-m-d'),
                    'group_id' => $group_id,
                    'type' => self::ACTIVITIES[$group_id][2], // согласия
                ])->get(),
        ];


        for($i = 1; $i <= $date->daysInMonth; $i++) {
            $arr[self::S_MINUTES][$i] = 0;
            foreach($asis[self::S_MINUTES] as $asi) {
                
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $arr[self::S_MINUTES][$i] += $data[$i];
                }
            }
            
        }


        ///
        $user_ids = json_decode(ProfileGroup::find(53)->users);
        $user_ids_1 = User::withTrashed()->whereIn('id', $user_ids)->where('position_id',32)->where('program_id', 1)->get()->pluck('id')->toArray();

        for($i = 1; $i <= $date->daysInMonth; $i++) {

            $arr[self::S_OPERATORS][$i] = 0;

            $ts_all = Timetracking::whereIn('user_id', $user_ids_1)
                ->whereDate('enter', $date->day($i)->format('Y-m-d'))
                ->whereNotNull('total_hours')
                //->where('program_id', 1)
                ->get()
                ->sum('total_hours');
       
            // $ts_all_p = Timetracking::whereIn('user_id', $user_ids_1)
            //     ->whereDate('enter', $date->day($i)->format('Y-m-d'))
            //     ->whereNull('program_id')
            //     ->sum('total_hours');

            $arr[self::S_OPERATORS][$i] = number_format(($ts_all) / 60 / 8, 1);
        }

        ///
        for($i = 1; $i <= $date->daysInMonth; $i++) {
            $arr[self::S_AGGREES][$i] = 0;
            foreach($asis[self::S_AGGREES] as $asi) {
                
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $arr[self::S_AGGREES][$i] += $data[$i];
                }
            }
        }
        
        // for($i = 1; $i <= $date->daysInMonth; $i++) {
        //     $arr[self::S_OPERATORS][$i] = 0;
        //     foreach($asis[self::S_MINUTES] as $asi) {
        //         $data = json_decode($asi->data, true);
        //         if(array_key_exists($i, $data) && (int)$data[$i] != 0) {
        //             $user = User::withTrashed()->find($asi->employee_id);
        //             if($user) {
        //                 $value = $user->full_time == 1 ? 1 : 0.5;
        //                 $arr[self::S_OPERATORS][$i] += $value;
        //             }
                    
        //         }
        //     }
        // }

        return $arr;
    }

    public static function getEmployeeHours($group_id, int $user_id, Carbon $date) {
        $day = (int)$date->format('d');
        
        $local_date = $date->format('Y-m-d');
        $asi = ASI::where([
            'date' => Carbon::parse($local_date)->startOfMonth()->format('Y-m-d'),
            'group_id' => $group_id,
            'type' => self::ACTIVITIES[$group_id][0], // минуты
            'employee_id' => $user_id, 
        ])->first();
        
        
        $hours = 0;
        if($asi) {
            $data = json_decode($asi->data, true);
            if(array_key_exists($day, $data)) {
                $hours = $data[$day];
            } 
        }
        
        return $hours;
    }   
    
    /**
     * Обновить часы работы и учет времени от количества действий
     * @param int $user_id 
     * @param String $date 'Y-m-d'
     * @param String $day 
     * @return void 
     */
    public static function updateTimes($group_id, int $user_id, $date, $day) {
       
    }


    /**
     *   Перерасчет отработанных минут оператора 
     */
    public static function getWorkedMinutes($user_email, $day) {

        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();
        
        $full_time = 0; // общее отработанное время
        $dialer_id = 398;
        
        if($account) {
            $call_account_id = $account->id;

            $ready_sec = 0; // время в статусе Готов
            $fill_sec = 0;  // время заполнения анкеты
            $call_sec = 0;  // время разговора
            
            
            $calls = DB::connection('callibro')->table('calls')
                    ->select('call_account_id', DB::raw('SUM(calls.billsec) as billsec_sum'))
                    ->whereDate('start_time', $day)
                    ->where('billsec', '>=', 10)
                    ->where('call_account_id', $call_account_id)
                    ->where('call_dialer_id', $dialer_id)
                    ->where('cause', '!=', 'SYSTEM_SHUTDOWN')
                    ->first();
                    

            $call_sec = $calls->billsec_sum;

            // $reports = DB::connection('callibro')->table('call_account_actions')
            //         ->select(
            //             DB::raw('SUM(state_duration) as state_duration'),
            //             'account_id',
            //             'operator_status_id',
            //             'state')
            //         ->where('account_id', $call_account_id)
            //         ->whereDate('created_at', $day)
            //         ->groupBy('state')->get();

            // foreach($reports as $report) {
            //     if($report->state == 'ready') { 
            //       $ready_sec = $report->state_duration;
            //     }
            //     if($report->state == 'fill') { 
            //       $fill_sec = $report->state_duration;
            //     }
            // }
          
            $full_time = (int) ceil(($call_sec) / 60); // отработанное время в минутах
            
        } 

        return $full_time;
        
    }

    /**
     *   Перерасчет отработанного времени оператора 
     */
    public static function getWorkedHours($user_email, $day) {

        $account = DB::connection('callibro')->table('call_account')->where('owner_uid', 5)->where('email', $user_email)->first();
        
        $full_time = 0; // общее отработанное время
        $dialer_id = 398;
        
        if($account) {
            $call_account_id = $account->id;

            $ready_sec = 0; // время в статусе Готов
            $fill_sec = 0;  // время заполнения анкеты
            $call_sec = 0;  // время разговора
            
            
            $calls = DB::connection('callibro')->table('calls')
                    ->select('call_account_id', DB::raw('SUM(calls.opersec) as billsec_sum'))
                    ->whereDate('start_time', $day)
                    //->where('opersec', '>=', 10)
                    ->where('call_account_id', $call_account_id)
                    ->where('call_dialer_id', $dialer_id)
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

    /**
     * Получить корректные согласия по сотруднику
     */
    public static function getAggrees($user_email, $day) {
        $account = DB::connection('callibro')->table('call_account')->where('email', $user_email)->first();
        
        $aggrees = 0; // общее отработанное время
        $dialer_id = 398;
        $script_status_ids = [2519]; // Cтатус в скрипте: Дата Визита

        if($account) {
            $call_account_id = $account->id;

            $aggrees = DB::connection('callibro')->table('calls')->select(
                'id',
                DB::raw("DATE_FORMAT(start_time, '%d.%m.%Y %H:%i:%s') as start_time"),
                'correct_or_not')
                ->whereDate('start_time', $day)
                // ->where(function($query) {
                //     $query->where('correct_or_not', 1)// 1 Корректный || 2 Некорректный  
                //         ->orWhereNull('correct_or_not', );
                // })
                ->where('correct_or_not', '!=', 2)
                ->where('call_account_id', $call_account_id)
                ->where('call_dialer_id', $dialer_id)
                ->whereIn('script_status_id', $script_status_ids)
                ->get()->count();
 
        } 

        return $aggrees;
    }
    
    
    /**
     * Получить СРЕДНЮЮ КОНВЕРСИЮ
     */
    public static function getConversionAvg(Carbon $date) {

        $local_date = $date->format('Y-m-d');

        $as = AnalyticsSettings::where([
            'date' => Carbon::parse($local_date)->startOfMonth()->format('Y-m-d'),
            'group_id' => self::ID,
        ])->first();
        
        $value = 0;

        $day = Carbon::parse($local_date)->day;

        if($as && array_key_exists(self::S_AVG_CONVERSION, $as->data)
               && array_key_exists('avg', $as->data[self::S_AVG_CONVERSION])) {
            $value = $as->data[self::S_AVG_CONVERSION]['avg'];
        }
        
        return (float)$value;
    }


    /**
     * Получить корректные согласия по сотруднику
     */
    public static function getClosedCards($day, $user_email = '') {

        $cards = 0; // общее отработанное время
        $dialer_id = 398;
        
        /**
         *  Cтатус в скрипте
         */
        $script_status_ids = [
            2519, // Дата визита
            2521, // В декрете
            2529, // Низкий доход
            2532, // Есть просрочка
            2533, // Нет пенсионных отчислений
            2534, // Инвалидность 1, 2 группы
            2536, // Военные Алматы/Алматинская обл
            2538, // Не интересует
            2539, // Негатив к Банку
            2540, // Не устраивают условия
            2541, // Подумает
            2542, // Интересует другой продукт (Автокредит)
            2543, // Интересует другой продукт (Рефинансирование)
            2544, // Интересует другой продукт (Ипотека)
            2545, // Интересует другой продукт (Депозит)
            2549, // Уточненный номер
            2551, // Клиент умер
            2552, // Не гражданин РК
            12275, // Неверный номер
            13015, // Согласился онлайн
        ]; 

        $cards = DB::connection('callibro')->table('calls')
            ->select(
                'id',
                DB::raw("DATE_FORMAT(start_time, '%d.%m.%Y %H:%i:%s') as start_time")
            )
            ->whereDate('start_time', $day)
            ->where('call_dialer_id', $dialer_id)
            ->whereIn('script_status_id', $script_status_ids)
            ->where('cause', '!=', 'NORMAL_TEMPORARY_FAILURE');
        
        if($user_email != '') {
            $account = DB::connection('callibro')->table('call_account')->where('email', $user_email)->first();
            if(!$account) return -1;
            $cards->where('call_account_id', $account->id);
        }
        
        return $cards->get()->count();
    }
}
