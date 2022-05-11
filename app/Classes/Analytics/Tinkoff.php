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

class Tinkoff 
{
    public $table1 = 'analytics_settings';
    public $table2 = 'analytics_settings_individually';

    /**
     * Группа Tinkoff
     */
    CONST ID = 46;
    CONST GROUP_ID = [46];
    CONST ACTIVITIES = [
        '46' => [
            42, // Минуты
            43, // Согласия
            44, // Рейтинг оператора
        ],
    ];

    /**
     * Поля сводной таблицы
     */
    CONST S_TOTAL = 0; // первая пустая строка
    CONST S_IMPL = 1; // impl
    CONST S_PRCSTLL = 2; // Pr, cstll
    CONST S_AGGREES = 3; // Факт согласий
    CONST S_AGGREES_PLAN = 4; // Plan согласий
    CONST S_HOURS = 5; // Факт минут
    CONST S_HOURS_PLAN = 6; // Plan минут
    CONST S_OPERATORS = 7; // Факт операторов
    CONST S_OPERATORS_PLAN = 8; // План операторов на линии
    CONST S_CONVERSION_AGGREES = 9; // Конверсия в согласия
    CONST S_CLOSED_CARDS = 10; // Закрыто карт в общем

    
    public static function defaultSummaryTable() {
        return [
            ['rownumber' => 2,'headers' => 0, 'pr' => 0,'plan' => 0],
            ['rownumber' => 3,'headers' => 'Impl', 'pr' => 0,'plan' => 0],
            ['rownumber' => 4,'headers' => 'Pr, cstll', 'pr' => 0,'plan' => 0],
            ['rownumber' => 5,'headers' => 'Факт согласий', 'pr' => 0,'avg' => 0],
            ['rownumber' => 6,'headers' => 'Plan согласий', 'pr' => 0,],
            ['rownumber' => 7,'headers' => 'Факт минут', 'pr' => 0,'avg' => 0],
            ['rownumber' => 8,'headers' => 'Plan минут','pr' => 0,],
            ['rownumber' => 9,'headers' => 'Факт операторов','avg' => 0],
            ['rownumber' => 10,'headers' => 'План операторов на линии', 'pr' => 0],
            ['rownumber' => 11,'headers' => 'Конверсия в согласия', 'avg' => 0],
            ['rownumber' => 12,'headers' => 'Закрыто карт в общем', 'pr' => 0],
        ];
    }

    public static function getTotals($group_id, Carbon $date) {
        $arr = [
            self::S_HOURS => [],
            self::S_AGGREES => [],
            self::S_OPERATORS => [],
        ];

        $asis = [
            self::S_HOURS =>  ASI::where([
                    'date' => $date->startOfMonth()->format('Y-m-d'),
                    'group_id' => $group_id,
                    'type' => self::ACTIVITIES[$group_id][0], // минуты
                ])->get(),
            self::S_AGGREES =>  ASI::where([
                    'date' => $date->startOfMonth()->format('Y-m-d'),
                    'group_id' => $group_id,
                    'type' => self::ACTIVITIES[$group_id][1], //согласия
                ])->get(),
        ];


        for($i = 1; $i <= $date->daysInMonth; $i++) {
            $arr[self::S_HOURS][$i] = 0;
            foreach($asis[self::S_HOURS] as $asi) {
                
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $arr[self::S_HOURS][$i] += $data[$i];
                }
            }
        }
        
        for($i = 1; $i <= $date->daysInMonth; $i++) {
            $arr[self::S_AGGREES][$i] = 0;
            foreach($asis[self::S_AGGREES] as $asi) {
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $arr[self::S_AGGREES][$i] += $data[$i];
                }
            }
        }

        $user_ids = json_decode(ProfileGroup::find(57)->users);

        for($i = 1; $i <= $date->daysInMonth; $i++) {

            $arr[self::S_OPERATORS][$i] = 0;

            $ts_all = Timetracking::whereIn('user_id', $user_ids)
                ->whereDate('enter', $date->day($i)->format('Y-m-d'))
                ->select('total_hours')
                ->get()
                ->sum('total_hours');

            $arr[self::S_OPERATORS][$i] = number_format(($ts_all) / 60 / 9, 1);
        }

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
     * Получить СРЕДНЮЮ КОНВЕРСИЮ
     */
    public static function getConversionAvg(Carbon $date) {

        $local_date = $date->format('Y-m-d');

        $as = AnalyticsSettings::where([
            'date' => Carbon::parse($local_date)->startOfMonth()->format('Y-m-d'),
            'group_id' => self::ID,
        ])->first();
        
        $value = 0;

        if($as && array_key_exists(self::S_CONVERSION_AGGREES, $as->data)
               && array_key_exists('avg', $as->data[self::S_CONVERSION_AGGREES])) {
            $value = $as->data[self::S_CONVERSION_AGGREES]['avg'];
        }
        
        return (float)$value;
    }
  
}
