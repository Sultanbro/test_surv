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

    protected $casts = [
        'data' => 'array',
    ];
    
    protected $fillable = [
        'name',
        'group_id',
        'daily_plan',
        'plan_unit', // метод расчета
        'unit', // ед изм 
        'ud_ves',
        'editable',
        'order',
        'type',
        'weekdays', // рабочие дни в неделе, для выставления плана на месяц
        'data' // дополнительно
    ];
    
 
    const UNIT_MINUTES = 1;  // сумма минут
    const UNIT_PERCENTS = 2; // сред значение
    const UNIT_LESS_SUM = 3; // сумма не более
    const UNIT_LESS_AVG = 4; // среднее не более. обратное для UNIT_PERCENTS
    const UNIT_MORE_SUM = 5; // сумма не менее
  

    const VIEW_DEFAULT = 0;
    const VIEW_COLLECTION = 1;
    const VIEW_QUALITY = 2;
    const VIEW_RENTAB = 3;
    const VIEW_TURNOVER = 4;
    const VIEW_STAFF = 5;
    const VIEW_CONVERSION = 6;

    
    const SOURCE_NO = 0; // без источника
    const SOURCE_GROUP = 1; // из показателей группы
    const SOURCE_BITRIX = 2; // из битрикса
    const SOURCE_AMOCRM = 3; // из амо

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
            if(!array_key_exists("lastname",$record)){
                $record['lastname'] = ' ';
            }
            if(!array_key_exists("group",$record)){
                $record['group'] = ' ';
            }
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
            if($unit == self::UNIT_PERCENTS) {
                $row['Сред'] = $count > 0 ? number_format($total / $count, 2) : '';
            } else {
                $row['Вып'] = $count > 0 && $total > 0 ? $total : '';
                $row['Сред'] = $count > 0 && $total > 0 ? number_format($total / $count, 2) : '';
                if(intval($row['План']) == 0 || intval($row['Вып']) == 0){
                    $row['%'] = 0;
                }else{
                    $row['%'] = intval($row['План']) / intval($row['Вып']) * 100;
                }
            }    
            for($i=1;$i<=$date->daysInMonth;$i++) {
                $row[$i] = array_key_exists($i, $record) ? $record[$i] . ' ' : '';
            }   
            $row['План'] = array_key_exists('plan',$record) ? $record['plan'] : '';
            if($row['План'] != '' && intval($row['Вып']) != 0 &&  intval($row['План']) != 0){
                $row['%'] = round((intval($row['Вып'] * 100)) / intval($row['План']),0) . '%';
            }
            array_push($sheet, $row);
        }
        return $sheet;
    }

    public static function createQuality($group_id){
        $act = self::where('group_id', $group_id)->where('type', 'quality')->first();
        if(!$act) {
            self::create([
                'name' => 'OKK',
                'group_id' => $group_id,
                'daily_plan' => 100,
                'plan_unit' => 'percent', // метод расчета
                'unit' => '', // ед изм 
                'ud_ves' => 0,
                'editable' => true,
                'order' => 0,
                'type' => 'quality',
            ]);
        }
    }

}
