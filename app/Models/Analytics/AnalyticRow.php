<?php

namespace App\Models\Analytics;

use App\DTO\Analytics\V2\CreateAnalyticDto;
use App\Helpers\DateHelper;
use App\ProfileGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property int $id
 * @property string $name
 * @property int $group_id
 * @property string $order
 * @property string $date
 * @property int $depend_id
 */
class AnalyticRow extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'group_id',
        'order',
        'date',
        'depend_id',
    ];

    public static function defaults($group_id, $date): void
    {
        $fields = [
            ProfileGroup::query()
                ->find($group_id)->name,
            'second',
            'Impl',
            'Pr, cstll',
            'Средняя конверсия',
            'План согласий',
            'Факт согласий',
            'Минуты операторов',
            'План операторов',
            'Факт операторов',
            '',
        ];

        $column = AnalyticColumn::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'name')
            ->first();

        $column_avg = AnalyticColumn::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'avg')
            ->first();

        $column_plan = AnalyticColumn::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'plan')
            ->first();

        $column_sum = AnalyticColumn::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'sum')
            ->first();

        $row_2 = 0;
        $row_3 = 0;
        $row_4 = 0;
        $row_7 = 0;
        $row_11 = 0;

        $order_index = count($fields);

        foreach ($fields as $index => $field) {
            $row = self::create([
                'group_id' => $group_id,
                'name' => $field,
                'date' => $date,
                'order' => $order_index--,
            ]);

            if ($index + 1 == 2) $row_2 = $row->id;
            if ($index + 1 == 3) $row_3 = $row->id;
            if ($index + 1 == 4) $row_4 = $row->id;
            if ($index + 1 == 7) $row_7 = $row->id;
            if ($index + 1 == 11) $row_11 = $row->id;

            if ($index + 1 == 2) continue;
            AnalyticStat::create([
                'group_id' => $group_id,
                'date' => $date,
                'row_id' => $row->id,
                'column_id' => $column->id,
                'value' => $field,
                'show_value' => $field,
                'editable' => 1, //in_array($field, ['Impl', 'Pr, cstll']) ? 0 : 1,
                'class' => 'text-left font-bold',
                'type' => AnalyticStat::INITIAL,
            ]);

            // if first row
            if ($index == 0) {
                $fields = [
                    'name',
                    'plan',
                    'sum',
                    'avg',
                ];

                foreach ($fields as $field) {
                    $col = AnalyticColumn::where('group_id', $group_id)
                        ->where('date', $date)
                        ->where('name', $field)
                        ->first();

                    $arr = [
                        'group_id' => $group_id,
                        'date' => $date,
                        'row_id' => $row->id,
                        'column_id' => $col->id,
                        'value' => $field,
                        'show_value' => $field,
                        'editable' => 1,
                        'class' => 'text-center font-bold bg-grey',
                        'type' => AnalyticStat::INITIAL,
                    ];

                    AnalyticStat::create($arr);
                }

                $datex = Carbon::parse($date);
                for ($i = 1; $i <= $datex->daysInMonth; $i++) { // days row

                    $col = AnalyticColumn::where('group_id', $group_id)
                        ->where('date', $date)
                        ->where('name', $i)
                        ->first();

                    AnalyticStat::create([
                        'group_id' => $group_id,
                        'date' => $date,
                        'row_id' => $row->id,
                        'column_id' => $col->id,
                        'value' => $i,
                        'show_value' => $i,
                        'class' => 'text-center font-bold bg-grey',
                        'editable' => 1,
                        'type' => AnalyticStat::INITIAL,
                    ]);
                }
            }

            if ($index >= 3) { // default sum and avg fields

                $arr = [
                    'group_id' => $group_id,
                    'date' => $date,
                    'row_id' => $row->id,
                    'editable' => 1,
                    'class' => 'text-center font-bold',
                ];


                if ($index == 3) { // C2
                    $arr['type'] = 'sum';
                    $arr['column_id'] = $column_sum->id;
                    $arr['row_id'] = $row_2;
                    $arr['value'] = 0;
                    $arr['show_value'] = 0;
                    $arr['class'] = 'text-center font-bold bg-yellow';
                } else {
                    $arr['type'] = 'sum';
                    $arr['column_id'] = $column_sum->id;
                    $arr['value'] = 0;
                    $arr['row_id'] = $row->id;
                    $arr['show_value'] = 0;
                    $arr['class'] = 'text-center font-bold';
                }

                AnalyticStat::create($arr);

                if ($index != 3) {
                    $arr['type'] = 'avg';
                    $arr['column_id'] = $column_avg->id;
                    $arr['value'] = 0;
                    $arr['show_value'] = 0;
                    $arr['row_id'] = $row->id;
                    $arr['class'] = 'text-center font-bold';

                    AnalyticStat::create($arr);
                }


            }

        }

        // add formulas  by default
        foreach ($fields as $index => $field) {

            $arr = [
                'group_id' => $group_id,
                'date' => $date,
                'show_value' => 0,
                'class' => 'text-center font-bold bg-yellow',
                'type' => 'formula',
            ];

            if ($index == 0) {
                $arr['row_id'] = $row_2;
                $arr['column_id'] = $column->id;
                $arr['editable'] = 1;
                $arr['value'] = '[' . $column_sum->id . ':' . $row_2 . '] - [' . $column_plan->id . ':' . $row_2 . ']';

                AnalyticStat::create($arr); // A2

                $arr['row_id'] = $row_2;
                $arr['column_id'] = $column_plan->id;
                $arr['editable'] = 1;
                $arr['value'] = 0;
                $arr['type'] = 'salary';


                AnalyticStat::create($arr); // B2

            }

            if ($index == 1) {

                $arr['row_id'] = $row_3;
                $arr['column_id'] = $column_plan->id;
                $arr['editable'] = 1;
                $arr['class'] = 'text-center';
                $arr['value'] = '[' . $column_sum->id . ':' . $row_2 . '] / [' . $column_plan->id . ':' . $row_4 . '] * 100';
                $arr['comment'] = 'Баланс выполнения';
                AnalyticStat::create($arr); // B3

                $arr['row_id'] = $row_3;
                $arr['column_id'] = $column_sum->id;
                $arr['editable'] = 1;
                $arr['value'] = '[' . $column_sum->id . ':' . $row_2 . '] / [' . $column_sum->id . ':' . $row_4 . '] * 100';
                $arr['comment'] = 'Процент выполнения месячного плана';
                AnalyticStat::create($arr); // C3

            }

            if ($index == 2) {

                $arr['row_id'] = $row_4;
                $arr['column_id'] = $column_plan->id;
                $arr['editable'] = 1;
                $arr['class'] = 'text-center';
                $arr['value'] = '[' . $column_sum->id . ':' . $row_11 . ']  * 250 * 8 * 3.5 / 1000';
                $arr['comment'] = 'Сколько на данный момент должны сделать';
                AnalyticStat::create($arr); // B4

                $arr['row_id'] = $row_4;
                $arr['column_id'] = $column_sum->id;
                $arr['editable'] = 1;
                $arr['value'] = '[' . $column_sum->id . ':' . $row_7 . ']  * 250 * 3.5 / 1000';
                $arr['comment'] = 'План на месяц';

                AnalyticStat::create($arr); // C4
            }
        }
    }

    /**
     * @param CreateAnalyticDto $dto
     * @return void
     * @throws Throwable
     */
    public static function createAnalyticsRows(CreateAnalyticDto $dto): void
    {
        try {
            DB::beginTransaction();

            $date   = Carbon::createFromDate($dto->year, $dto->month);
            $firstDayOfMonth = $date->firstOfMonth()->toDateString();

            $fields = ['name', 'plan', 'sum', 'avg'];
            $columns = AnalyticColumn::query()->where([
                'group_id'  => $dto->groupId,
                'date'      => $firstDayOfMonth
            ])->get();

            $row = self::query()->create([
                'group_id'  => $dto->groupId,
                'name'      => $dto->rows['name'],
                'date'      => $firstDayOfMonth,
                'order'     => 1,
            ]);
            /**
             * Создаем в таблице аналитики данные.
             */
            $stats = $columns->whereIn('name', $fields)->map(function ($column) use (
                $row,
                $dto,
                $date
            ) {
                return [
                    'group_id'  => $dto->groupId,
                    'date'      => $date->firstOfMonth()->toDateString(),
                    'row_id'    => $row->id,
                    'column_id' => $column->id,
                    'value'     => $column->name,
                    'show_value' => $column->name,
                    'editable'  => 1,
                    'class'     => 'text-center font-bold bg-grey',
                    'type'      => AnalyticStat::INITIAL,
                ];
            })->toArray();

            for ($day = 1; $day <= $date->daysInMonth; $day++)
            {
                $col = $columns->where('name', $day)->first();
                $stats[] = [
                    'group_id' => $dto->groupId,
                    'date' => $firstDayOfMonth,
                    'row_id' => $row->id,
                    'column_id' => $col->id,
                    'value' => $day,
                    'show_value' => $day,
                    'class' => 'text-center font-bold bg-grey',
                    'editable' => 1,
                    'type' => AnalyticStat::INITIAL,
                ];
            }

            AnalyticStat::query()->insert($stats);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
