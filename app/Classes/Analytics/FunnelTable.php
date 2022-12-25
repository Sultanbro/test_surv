<?php
namespace App\Classes\Analytics;

use App\AnalyticsSettings;
use Carbon\Carbon;
use App\Classes\Analytics\Recruiting;

class FunnelTable 
{
    /**
     * Таблицы с воронками
     */
    public static function getTables($date) {
        $tables = [];

        $tables['all']['all'] = self::getTemplate('segments');

        $tables['all']['hh'] = self::getTemplate('hh');
        $tables['all']['insta'] = self::getTemplate('insta');

        for($i=1;$i<=12;$i++) { 
            $local_date = Carbon::parse($date)->month($i)->format('Y-m-d');

            $tables['month'][$i]['hh'] = self::getTable('hh', $local_date);
            $tables['month'][$i]['insta'] = self::getTable('insta', $local_date);
        }   
        

        /** */
        for($i=1;$i<=12;$i++) {
            for($j=0;$j<=13;$j++) {
                $tables['all']['all'][$j][$i] = 0; 
            }
        }
       
        /** */
        for($i=1;$i<=12;$i++) {
            $cdate = Carbon::parse($date)->month($i);
            $weeks = self::getWeeksNumber($i, $cdate);

            $cdate = Carbon::parse($date)->month($i);

            $hh = AnalyticsSettings::where('date', $cdate)
                ->where('group_id', Recruiting::GROUP_ID)
                ->where('type', 'hh')
                ->first(); 

            if($hh) { 
                $data = $hh->data;
                
                $value0 = 0;
                $value1 = 0;
                $value2 = 0;
                $value3 = 0;
                $value4 = 0;
                $value8 = 0;
                

                for($j=1;$j<=$weeks;$j++) {
                    $value0 += array_key_exists($j, $data[0]) ? (int)$data[0][$j]  : 0;
                    $value1 += array_key_exists($j, $data[1]) ? (int)$data[1][$j]  : 0;
                    $value2 += array_key_exists($j, $data[2]) ? (int)$data[2][$j]  : 0;
                    $value3 += array_key_exists($j, $data[3]) ? (int)$data[3][$j]  : 0;
                    $value4 += array_key_exists($j, $data[4]) ? (int)$data[4][$j]  : 0;
                    $value8 += array_key_exists($j, $data[8]) ? (int)$data[8][$j]  : 0;
                }
                
                $tables['all']['hh'][0][$i] = $value0; // Затраты
                $tables['all']['hh'][1][$i] = $value1; // Создано лидов
                $tables['all']['hh'][2][$i] = $value2; // Сконвертировано
                $tables['all']['hh'][3][$i] = $value3; // Стажируется 1 день
                $tables['all']['hh'][4][$i] = $value4; // Стажиуется 2 день
                $tables['all']['hh'][8][$i] = $value8; // Приняты в штат

                $tables['all']['all'][0][$i] += $value0; // Затраты
                $tables['all']['all'][4][$i] += $value1; // Создано лидов
                $tables['all']['all'][5][$i] += $value2; // Сконвертировано
                $tables['all']['all'][6][$i] += $value3; // Стажируется 1 день
                $tables['all']['all'][7][$i] += $value4; // Стажиуется 2 день
                $tables['all']['all'][12][$i] += $value8; // Приняты в штат
                
            }

            $insta = AnalyticsSettings::where('date', $cdate)
                ->where('group_id', Recruiting::GROUP_ID)
                ->where('type', 'insta')
                ->first(); 


            if($insta) {
                $data = $insta->data;

                $value0 = 0; 
                $value1 = 0; 
                $value2 = 0; 
                $value3 = 0; 
                $value4 = 0;
                $value5 = 0; 
                $value6 = 0;
                $value7 = 0;
                $value12 = 0;
                
                for($j=1;$j<=$weeks;$j++) {
                    $value0 += array_key_exists($j, $data[0]) ? $data[0][$j]  : 0;
                    $value1 += array_key_exists($j, $data[1]) ? $data[1][$j]  : 0;
                    $value2 += array_key_exists($j, $data[2]) ? $data[2][$j]  : 0;
                    $value3 += array_key_exists($j, $data[3]) ? $data[3][$j]  : 0;
                    $value4 += array_key_exists($j, $data[4]) ? $data[4][$j]  : 0;
                    $value5 += array_key_exists($j, $data[5]) ? $data[5][$j]  : 0;
                    $value6 += array_key_exists($j, $data[6]) ? $data[6][$j]  : 0;
                    $value7 += array_key_exists($j, $data[7]) ? $data[7][$j]  : 0;
                    $value12 += array_key_exists($j, $data[12]) ? $data[12][$j]  : 0;
                }

                $tables['all']['insta'][0][$i] = $value0;  // Затраты
                $tables['all']['insta'][1][$i] = $value1; // охват
                $tables['all']['insta'][2][$i] = $value2;// показы
                $tables['all']['insta'][3][$i] = $value3; // переходы
                $tables['all']['insta'][4][$i] = $value4; // Создано лидов
                $tables['all']['insta'][5][$i] = $value5; // Сконвертировано
                $tables['all']['insta'][6][$i] = $value6; // Стажируется 1 день
                $tables['all']['insta'][7][$i] = $value7; // Стажируется 2 день
                $tables['all']['insta'][12][$i] = $value12;// Приняты в штат

                $tables['all']['all'][0][$i] += $value0;  // Затраты
                $tables['all']['all'][1][$i] += $value1; // охват
                $tables['all']['all'][2][$i] += $value2;// показы
                $tables['all']['all'][3][$i] += $value3; // переходы
                $tables['all']['all'][4][$i] += $value4; // Создано лидов
                $tables['all']['all'][5][$i] += $value5; // Сконвертировано
                $tables['all']['all'][6][$i] += $value6; // Стажируется 1 день
                $tables['all']['all'][7][$i] += $value7; // Стажируется 2 день
                $tables['all']['all'][12][$i] += $value12;// Приняты в штат
            }
            
            


        }   

        return $tables;
    }

    /**
     * Получить недельную таблицу
     * @return array
     */
    public static function getTable($template, $date) {

        $table = self::getTemplate($template);

        $hh = AnalyticsSettings::where('date', $date)
            ->where('group_id', Recruiting::GROUP_ID)
            ->where('type', $template)
            ->first(); 

        if($hh) {
            $table = $hh->data;
        }

        return $table;

    }

    public static function getTemplate($title) {

        $arr = [];


        if($title == 'segments') {
            $arr =  [
                [
                    'name' => 'Затраты на рекламу',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Охват',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Показы',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Переходы',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Создано новых лидов',
                    'totals' => '0',
                    'show' => '0',
                ], 
                [
                    'name' => 'Сконвертировано', 
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (1 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (2 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'СРС (цена за клик)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CPL (цена за лид)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR1 (Создано лидов / Сконвертирован)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR2 (Сконвертирован / Приняты)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Приняты в штат',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CAC (Стоимость привлечения оператора)',
                    'totals' => '0',
                    'show' => '0',
                ],
                
            ];
        }

        /**
         * headhunter
         */
        if($title == 'hh') {
            $arr =  [
                [
                    'name' => 'Затраты на рекламу',
                    'totals' => '0',
                    'show' => '1',
                ],
                [
                    'name' => 'Создано новых лидов',
                    'totals' => '0',
                    'show' => '0',
                ], 
                [
                    'name' => 'Сконвертировано', 
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (1 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (2 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CPL (цена за лид)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR1 (Создано лидов / Сконвертирован)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR2 (Сконвертирован / Приняты)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Приняты в штат',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CAC (Стоимость привлечения оператора)',
                    'totals' => '0',
                    'show' => '0',
                ],
                
            ];
        }

        /**
         * target insta
         */
        if($title == 'insta') {
            $arr = [
                [
                    'name' => 'Затраты на рекламу',
                    'totals' => '0',
                    'show' => '1',
                ],
                [
                    'name' => 'Охват',
                    'totals' => '0',
                    'show' => '1',
                ],
                [
                    'name' => 'Показы',
                    'totals' => '0',
                    'show' => '1',
                ],
                [
                    'name' => 'Переходы',
                    'totals' => '0',
                    'show' => '1',
                ],
                [
                    'name' => 'Создано новых лидов',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Сконвертировано',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (1 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (2 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'СРС (цена за клик)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CPL (цена за лид)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR1 (Создано лидов / Сконвертирован)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR2 (Сконвертирован / Приняты)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Приняты в штат',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CAC (Стоимость привлечения оператора)',
                    'totals' => '0',
                    'show' => '0',
                ],
            ];
        }

        /**
         * target alina
         */
        if(in_array($title, ['alina', 'saltanat', 'akzhol', 'darkhan'])) {
            $arr = [
                [
                    'name' => 'Сконвертировано',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (1 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируется (2 день)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Стажируются сейчас',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'CR2 (Сконвертирован / Приняты)',
                    'totals' => '0',
                    'show' => '0',
                ],
                [
                    'name' => 'Приняты в штат',
                    'totals' => '0',
                    'show' => '0',
                ], 
            ];
        }

        return $arr;
    }


    /**
     * Сформировать и Получить годовую таблицу из месячных
     */
    public static function fillYear($month, Carbon $date) {

    }

    /** 
     * Количество недель в месяце 
    */
    public static function getWeeksNumber($month, Carbon $date) {
        if($month == 1) {
            $start = 1;
        } else {
            $start = $date->startOfMonth()->weekOfYear;
        }
  
        return $date->endOfMonth()->weekOfYear - $start + 1;
    }

    /**
     * Номер недели в месяце
     */
    public static function getWeekNumber($month, Carbon $date) {
        if($month == 1) {
            $start = 1;
        } else {
            $start = $date->startOfMonth()->weekOfYear;
        }
        return $date->weekOfYear - $start + 1;
    }

    public static function getAlina() {
        
    }
    
}
