<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSettings extends Model
{
    protected $dates = ['date'];
    
    protected $casts = [
        'data' => 'array',
        'users' => 'array',
        'extra' => 'array', // чтобы хранить некоторые показатели
    ];

    protected $fillable = [
        'date',
        'group_id',
        'user_id',
        'data',
        'type',
        'users',
        'extra'
    ];

    /**
     * Сформировать шапку для листа для ексель
     */
    public static function getHeadings(int $group_id, $date) {
        $arr = [];

        array_push($arr, '');
        array_push($arr, '');
        array_push($arr, 'pr');
        array_push($arr, 'plan');
        array_push($arr, 'сред');
        for($i=1;$i<=$date->daysInMonth;$i++) {
            array_push($arr, $i);
        }
        return $arr;
    }

    /**
     * Сформировать сводный лист для ексель
    */
    public static function getSheet(int $group_id, $date) {
        $settings = self::where([
            'group_id' =>$group_id,
            'date' =>$date->format('Y-m-d'),
        ])->first();
        
        $summary = [];
       
        if($settings) {

            foreach ($settings->data as $key => $row) {
                $arr = [];
                array_push($arr, array_key_exists('headers', $row) ? $row['headers'] : '');
                array_push($arr,array_key_exists('pr', $row) ? $row['pr'] : '');
                array_push($arr,array_key_exists('plan', $row) ? $row['plan'] : '');
                array_push($arr,array_key_exists('cst', $row) ? $row['cst'] : '');
               
                for($i=1;$i<=$date->daysInMonth;$i++) {
                    array_push($arr,array_key_exists($i, $row) ? $row[$i] : '');
                }

                array_push($summary, $arr);
            }

        }
        return $summary;
    }
}
