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

class Ozon 
{
    public $table1 = 'analytics_settings';
    public $table2 = 'analytics_settings_individually';

    /**
     * Группа Озон
     */
    CONST GROUP_ID = [58,59];
    CONST ACTIVITIES = [
        '58' => [
            25, // Время нахождения на линии
            26, // Время обработки
            29, // RR %
            33, // Средний рейтинг МФ_(аутсорс)
            34  // Закрыто тикетов
        ],
        '59' => [
            27, // Время нахождения на линии
            28, // Время обработки
            30, // RR %
            35, // Средний рейтинг МФ_(аутсорс)
            36  // Закрыто тикетов
        ],
    ];

    /**
     * Поля сводной таблицы
     */
    CONST S_TOTAL = 0; // первая пустая строка
    CONST S_IMPL = 1; // impl
    CONST S_PRCSTLL = 2; // Pr, cstll
    CONST S_DIALOGS = 3; // Всего принято диалогов
    CONST S_HOURS = 4; // Факт часов
    CONST S_PLAN = 5; // Plan
    CONST S_PROCESSED = 6; // Кол-во обработанных контактов 
    CONST S_DIALOGS_AVG = 7; // Ср. Отработка диал-в на оператора
    CONST S_MANAGER_PLAN = 8; // План менеджеров
    CONST S_FACT = 9; // Факт операторов

    
    public static function defaultSummaryTable() {
        return [
            ['rownumber' => 2,'headers' => 0, 'pr' => 0,'plan' => 0],
            ['rownumber' => 3,'headers' => 'Impl', 'pr' => 0,'plan' => 0],
            ['rownumber' => 4,'headers' => 'Pr, cstll', 'pr' => 0,'plan' => 0],
            ['rownumber' => 5,'headers' => 'Всего принято диалогов', 'pr' => 0,'avg' => 0],
            ['rownumber' => 6,'headers' => 'Факт часов', 'pr' => 0,'avg' => 0],
            ['rownumber' => 7,'headers' => 'Plan', 'pr' => 0,'avg' => 0],
            ['rownumber' => 8,'headers' => 'Кол-во обработанных контактов ',],
            ['rownumber' => 9,'headers' => 'Ср. Отработка диал-в на оператора','avg' => 0],
            ['rownumber' => 10,'headers' => 'План менеджеров', 'pr' => 0,'avg' => 0],
            ['rownumber' => 11,'headers' => 'Факт операторов', 'pr' => 0,'avg' => 0],
        ];
    }

    public static function getTotals($group_id, Carbon $date) {
        $arr = [
            self::S_HOURS => [],
            self::S_PROCESSED => [],
            self::S_DIALOGS => [],
        ];

        $asis = [
            self::S_HOURS =>  ASI::where([
                    'date' => $date->startOfMonth()->format('Y-m-d'),
                    'group_id' => $group_id,
                    'type' => self::ACTIVITIES[$group_id][0], // часы работы
                ])->get(),
            self::S_PROCESSED =>  ASI::where([
                    'date' => $date->startOfMonth()->format('Y-m-d'),
                    'group_id' => $group_id,
                    'type' => self::ACTIVITIES[$group_id][4], // колво действий
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
            $arr[self::S_PROCESSED][$i] = 0;
            foreach($asis[self::S_PROCESSED] as $asi) {
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $arr[self::S_PROCESSED][$i] += $data[$i];
                }
            }
        }

        for($i = 1; $i <= $date->daysInMonth; $i++) {
            $arr[self::S_DIALOGS][$i] = 0;
            foreach($asis[self::S_PROCESSED] as $asi) {
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $arr[self::S_DIALOGS][$i] += $data[$i];
                }
            }
        }

        return $arr;
    }

    public static function getEmployeeHours($group_id, int $user_id, Carbon $date) {
        $day = (int)$date->format('d');
        
        $local_date = $date->format('Y-m-d');
        $asi = ASI::where([
            'date' => Carbon::parse($local_date)->startOfMonth()->format('Y-m-d'),
            'group_id' => $group_id,
            'type' => self::ACTIVITIES[$group_id][0], // учет времени
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
        $setting = ASI::where(['date' => $date, 'group_id' => $group_id, 'employee_id' => $user_id, 'type' => self::ACTIVITIES[$group_id][0]])->first();

        if($setting) {
            $data = json_decode($setting->data, true);
     
            $carbon_date = Carbon::parse($date)->day($day);
            
            $tt = Timetracking::where('user_id', $user_id)
                ->whereYear('enter', $carbon_date->year)
                ->whereMonth('enter', $carbon_date->month)
                ->whereDay('enter', $day)
                ->orderBy('id', 'desc')->first();
            
            $hours = $data[$day];

            if($tt) {
                $tt->total_hours = $hours * 60;
                $tt->updated = 1;
                $tt->save();
            } else {
                Timetracking::create([
                    'enter' => $carbon_date,
                    'user_id' => $user_id,
                    'updated' => 1,
                    'total_hours' => $hours * 60,
                ]);
            }

            TimetrackingHistory::create([
                'author_id' => Auth::user()->id,
                'author' => Auth::user()->name.' '.Auth::user()->last_name,
                'user_id' => $user_id,
                'description' => 'Изменено время с Аналитики на ' . $hours,
                'date' => $carbon_date->format('Y-m-d')
            ]);
        }
    }

    /**
     *  Avg of some activity for month
     */
    public static function getAvgHours(Carbon $date) {
        $as = AnalyticsSettings::where([
            'date' => $date->startOfMonth()->format('Y-m-d'),
            'group_id' => self::GROUP_ID[0],
        ])->first();

        $total = 0;
        $count = 0;
        if($as) {
            $data = $as->data[self::S_DIALOGS_AVG];
            for($i = 1; $i <= $date->daysInMonth; $i++) {
                if(array_key_exists($i, $data)) {
                    $total += $data[$i];
                    if((float) $data[$i] > 0) $count++;
                }
            }

        }

        return $count > 0 ? round($total / $count, 1) : 0;
    }

    /**
     * Avg of some activity for month
     * index of activity
     */
    public static function getAvg(Carbon $date, $index) {
        $asis = ASI::where([
            'date' => $date->startOfMonth()->format('Y-m-d'),
            'group_id' => 58,
            'type' => self::ACTIVITIES['58'][$index], // часы работы
        ])->get();
        
        $total = 0;
        $count = 0;

        for($i = 1; $i <= $date->daysInMonth; $i++) {
            foreach($asis as $asi) {
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $total += $data[$i];
                    if((float) $data[$i] > 0) $count++;
                }
            }
        }

        return $count > 0 ? round($total / $count, 1) : 0;
    }

    /**
     * Avg of some activity for month
     * index of activity
     */
    public static function getAvgClosed(Carbon $date) {
        $asi = ASI::where([
            'date' => $date->startOfMonth()->format('Y-m-d'),
            'group_id' => 58,
            'type' => self::ACTIVITIES['58'][4], // закрыто карточек
        ])->first();

        $plan = 0;
        if($asi) {
            $data = json_decode($asi->data, true);
            if(array_key_exists('month', $data)) {
                $plan = $data['month'];
            }
        }
        
        $closed_tickets = self::getAvg($date, 4);

        return $plan != 0 ? number_format($closed_tickets / $plan * 100, 2) : 0;
    }
    
    /**
     * Stat of some activity for day
     * index of activity
     */
    public static function getDayStats(Carbon $date, $index) {
        $day = $date->day;

        $asis = ASI::where([
            'date' => $date->startOfMonth()->format('Y-m-d'),
            'group_id' => 58,
            'type' => self::ACTIVITIES['58'][$index], // часы работы
        ])->get();
        
        $total = 0;
        $count = 0;

        foreach($asis as $asi) {
            if($asi) {
                $data = json_decode($asi->data, true);
                if(array_key_exists($day, $data)) {
                    $total += (float)$data[$i];
                }
            }
        }
        
        return  $count > 0 ? round($total / $count, 1) : 0;
    }

    public static function getHoursPlan(Carbon $date) {
        
    }
  
}
