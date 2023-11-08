<?php

namespace App\Models\Analytics;

use App\Models\Kpi\Traits\WithCreatorAndUpdater;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int $group_id
 * @property string $daily_plan
 * @property string $plan_unit
 * @property string $unit
 * @property string $ud_ves
 * @property string $share
 * @property string $method
 * @property string $view
 * @property string $source
 * @property string $editable
 * @property int $order
 * @property string $type
 * @property string $weekdays
 * @property string $data
 * @property int $created_by
 * @property int $updated_by
 * @property string $common
 */
class Activity extends Model
{
    use SoftDeletes, WithCreatorAndUpdater, HasFactory;

    protected $table = 'activities';

    public $timestamps = true;

    protected $casts = [
        'data' => 'array',
        'created_at' => 'date:d.m.Y H:i',
        'updated_at' => 'date:d.m.Y H:i',
    ];

    protected $fillable = [
        'name',
        'group_id',
        'daily_plan', // plan, потом переименовать
        'plan_unit', // метод расчета
        'unit', // ед изм 
        'ud_ves',
        'share',
        'method',
        'view',
        'source',
        'editable',
        'order',
        'type',
        'weekdays', // рабочие дни в неделе, для выставления плана на месяц
        'data', // дополнительно
        'created_by', // 
        'updated_by', // 
        'common' // показатели всей группы  = 1 или идивидуальный = 0
    ];

    // old consts for plan_units
    const UNIT_MINUTES = 1;  // сумма минут
    const UNIT_PERCENTS = 2; // сред значение
    const UNIT_LESS_SUM = 3; // сумма не более
    const UNIT_LESS_AVG = 4; // среднее не более. обратное для UNIT_PERCENTS
    const UNIT_MORE_SUM = 5; // сумма не менее

    /**
     * Methods
     */
    const METHOD_SUM = 1;  // сумма минут
    const METHOD_AVG = 2; // сред значение
    const METHOD_SUM_NOT_MORE = 3; // сумма не более
    const METHOD_AVG_NOT_MORE = 4; // среднее не более. обратное для UNIT_PERCENTS
    const METHOD_SUM_NOT_LESS = 5; // сумма не менее
    const METHOD_AVG_NOT_LESS = 6; // сумма не более

    /**
     * Views
     */
    const VIEW_DEFAULT = 0;
    const VIEW_COLLECTION = 1;
    const VIEW_QUALITY = 2;
    const VIEW_RENTAB = 3;
    const VIEW_TURNOVER = 4;
    const VIEW_STAFF = 5;
    const VIEW_CONVERSION = 6;
    const VIEW_CELL = 7;

    /**
     * Sources
     */
    const SOURCE_GROUP = 1; // из показателей группы
    const SOURCE_BITRIX = 2; // из битрикса
    const SOURCE_AMOCRM = 3; // из амо
    const SOURCE_LOCAL = 4; // другие
    const SOURCE_TIMEBOARD = 5; // вкладка табель
    const SOURCE_HR = 6; // вкладка HR

    /**
     * @return HasMany
     */
    public function plans(): HasMany
    {
        return $this->hasMany(ActivityPlan::class);
    }

    public static function getMethod(int $method_id)
    {
        $methods = [
            1 => 'sum',
            2 => 'avg',
            3 => 'sum_not_more',
            4 => 'avg_not_more',
            5 => 'sum_not_less',
            6 => 'avg_not_less',
        ];

        return array_key_exists($method_id, $methods) ? $methods[$method_id] : 'not_found';
    }

    /**
     * Получить заголовки для эксель
     */
    public static function getHeadings(Carbon $date, int $unit = 1, $is_kaspi_sum = false)
    {
        if ($unit == self::UNIT_PERCENTS) {
            $headings = [
                'Имя сотрудника', // 0
                'Отдел', // 1
                //   '', // 2
                'План', // 2
                'Сред', // 1
            ];
        } else {
            $headings = [
                'Имя сотрудника', // 0
                'Отдел', // 1
                // '', // 2
                'План', // 2
                'Вып', // 3
                'Сред', // 1
                '%',
            ];
        }

        if ($is_kaspi_sum) {
            $headings = [
                'Имя сотрудника', // 0
                'Отдел', // 1
                'К выдаче', // 2
                'Вып', // 3
                'Сред', // 1
                '%',
            ];
        }

        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            array_push($headings, $i);
        }

        return $headings;
    }


    /**
     * Сформировать лист для ексель
     */
    public static function getSheet($records, Carbon $date, int $unit = 1, $is_kaspi_sum = false)
    {
        $sheet = [];
        foreach ($records as $record) {
            $row = [];
            if (!array_key_exists("lastname", $record)) {
                $record['lastname'] = ' ';
            }
            if (!array_key_exists("group", $record)) {
                $record['group'] = ' ';
            }
            $row['Имя сотрудника'] = $record['lastname'] . ' ' . $record['name'];
            $row['Отдел'] = $record['group'];


            // if($unit == self::UNIT_PERCENTS) {
            //     $row['Ср.'] = array_key_exists('plan', $record) ? $record['plan'] : '';
            // } else {
            //     $row['Ср.'] = array_key_exists('avg', $record) ? $record['avg'] : '';
            //     $row['Вып'] = array_key_exists('plan', $record) ? $record['plan'] : '';
            //     $row['%'] = array_key_exists('percent', $record) ? $record['percent'] : '';
            // }

            $total = 0;
            $count = 0;
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $total += array_key_exists($i, $record) ? $record[$i] : 0;
                $count += array_key_exists($i, $record) && $record[$i] ? 1 : 0;
            }
            if ($is_kaspi_sum) {
                $row['К выдаче'] = $count > 0 && $total > 0 ? $total * 50 : '';
            } else {
                $row['План'] = array_key_exists('month', $record) ? $record['month'] : '';
            }
            if ($unit == self::UNIT_PERCENTS) {
                $row['Сред'] = $count > 0 ? number_format($total / $count, 2) : '';
            } else {
                $row['Вып'] = $count > 0 && $total > 0 ? $total : '';
                $row['Сред'] = $count > 0 && $total > 0 ? number_format($total / $count, 2) : '';
                if (intval($row['План']) == 0 || intval($row['Вып']) == 0) {
                    $row['%'] = 0;
                } else {
                    $row['%'] = intval($row['План']) / intval($row['Вып']) * 100;
                }
            }
            for ($i = 1; $i <= $date->daysInMonth; $i++) {
                $row[$i] = array_key_exists($i, $record) ? $record[$i] . ' ' : '';
            }
            $row['План'] = array_key_exists('plan', $record) ? $record['plan'] : '';
            if ($row['План'] != '' && intval($row['Вып']) != 0 && intval($row['План']) != 0) {
                $row['%'] = round((intval($row['Вып'] * 100)) / intval($row['План']), 0) . '%';
            }
            array_push($sheet, $row);
        }
        return $sheet;
    }

    public static function createQuality($group_id)
    {
        $act = self::where('group_id', $group_id)->where('type', 'quality')->first();
        if (!$act) {
            self::create([
                'name' => 'OKK',
                'group_id' => $group_id,
                'daily_plan' => 100,
                'plan_unit' => 'percent',
                'method' => self::METHOD_SUM,
                'unit' => '',
                'ud_ves' => 0,
                'editable' => true,
                'order' => 0,
                'source' => self::SOURCE_GROUP,
                'type' => 'quality',
                'view' => self::VIEW_QUALITY,
            ]);
        }
    }

    /**
     * get Quality table id
     */
    public static function qualityId(int $group_id)
    {
        $act = Activity::withTrashed()
            ->where('group_id', $group_id)
            ->where('view', self::VIEW_QUALITY)
            ->first();

        if (!$act) {
            $act = Activity::create([
                'name' => 'OKK',
                'group_id' => $group_id,
                'daily_plan' => 100,
                'source' => self::SOURCE_GROUP,
                'plan_unit' => 'percent',
                'method' => self::METHOD_SUM,
                'unit' => '',
                'ud_ves' => 0,
                'editable' => true,
                'order' => 0,
                'type' => 'quality',
                'view' => self::VIEW_QUALITY,
            ]);
        }

        return $act->id;
    }

}
