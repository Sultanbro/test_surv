<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Activity extends Model
{
    use SoftDeletes;
    protected $table = 'activities';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'group_id',
        'daily_plan',
        'plan_unit', // метод расчета
        'unit', // ед изм 
        'ud_ves'
    ];
    
    const UNIT_MINUTES = 1;
    const UNIT_PERCENTS = 2;
    const UNIT_LESS_SUM = 3;
    const UNIT_LESS_AVG = 4;

    /**
     * Получить заголовки для эксель
     */
    public static function getHeadings(Carbon $date, int $unit = 1, $is_kaspi_sum = false) {
        if($unit == self::UNIT_PERCENTS) {
            $headings = [
                'Имя сотрудника', // 0
                'Группа', // 1
             //   '', // 2
                'План', // 2
                'Сред', // 1
            ];
        } else {
            $headings = [
                'Имя сотрудника', // 0
                'Группа', // 1
               // '', // 2
                'План', // 2
                'Вып', // 3
                'Сред', // 1
                '%', 
            ];
        }
        
        if($is_kaspi_sum) {
            $headings = [
                'Имя сотрудника', // 0
                'Группа', // 1
                'К выдаче', // 2
                'Вып', // 3
                'Сред', // 1
                '%', 
            ];
        }

        for($i=1;$i<=$date->daysInMonth;$i++) {
            array_push($headings, $i);
        }
       
        return $headings;
    }


     /**
     * Сформировать лист для ексель
     */
    public static function getSheet($records, Carbon $date, int $unit = 1, $is_kaspi_sum = false) {
        $sheet = [];
        
        foreach ($records as $record) {
            $row = [];

            $row['Имя сотрудника'] = $record['lastname'] . ' ' . $record['name'];
            $row['Группа'] = $record['group'];
            

            // if($unit == self::UNIT_PERCENTS) {
            //     $row['Ср.'] = array_key_exists('plan', $record) ? $record['plan'] : '';
            // } else {
            //     $row['Ср.'] = array_key_exists('avg', $record) ? $record['avg'] : '';
            //     $row['Вып'] = array_key_exists('plan', $record) ? $record['plan'] : '';
            //     $row['%'] = array_key_exists('percent', $record) ? $record['percent'] : '';
            // }
 
            $total = 0;
            $count = 0;
            for($i=1;$i<=$date->daysInMonth;$i++) {
                $total += array_key_exists($i, $record) ? $record[$i] : 0;
                $count += array_key_exists($i, $record) && $record[$i] ? 1 : 0;
            }
            

            if($is_kaspi_sum) {
                $row['К выдаче'] = $count > 0 && $total > 0 ? $total * 50 : '';
            } else {
                $row['План'] = array_key_exists('month', $record) ? $record['month'] : '';
            }
            
            //if($total == 37 && $record['name'] == 'Али') dd($record);
            if($unit == self::UNIT_PERCENTS) {
                $row['Сред'] = $count > 0 ? number_format($total / $count, 2) : '';
            } else {
                $row['Вып'] = $count > 0 && $total > 0 ? $total : '';
                $row['Сред'] = $count > 0 && $total > 0 ? number_format($total / $count, 2) : '';
                $row['%']   = $count > 0 && $total > 0 && array_key_exists('month', $record) && $record['month'] > 0 ? number_format((float)$total / (float)$record['month'] * 100, 2) . '%' : '';
            }    
            
            

            for($i=1;$i<=$date->daysInMonth;$i++) {
                $row[$i] = array_key_exists($i, $record) ? $record[$i] . ' ' : '';
            }
            
            array_push($sheet, $row);
        }

        return $sheet;
    }

    

}
