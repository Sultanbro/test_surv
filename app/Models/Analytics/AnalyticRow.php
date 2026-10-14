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
        /** @var ProfileGroup $group */
        $group = ProfileGroup::query()->findOrFail($group_id);
        $rowsToCreate = [
            $group->name,
            'second'
        ];
        $order_index = count($rowsToCreate);

        $columns = AnalyticColumn::query()
            ->where('date', $date)
            ->where('group_id', $group_id)
            ->get();

        $rows = collect();
        foreach ($rowsToCreate as $create) {
            /** @var AnalyticRow $row */
            $rows->add(self::query()->create([
                'group_id' => $group_id,
                'name' => $create,
                'date' => $date,
                'order' => $order_index--,
            ]));
        }

        $firstRow = $rows->where('group_id', $group_id)->first();

        foreach ($columns as $column) {
            foreach ($rows as $row) {
                if ($row->id == $firstRow->id) {
                    AnalyticStat::query()
                        ->create([
                            'group_id' => $group_id,
                            'date' => $date,
                            'row_id' => $firstRow->id,
                            'column_id' => $column->id,
                            'value' => $column->name === 'name' ? $firstRow->name : $column->name,
                            'show_value' => $column->name,
                            'editable' => 1,
                            'class' => 'text-left font-bold',
                            'type' => AnalyticStat::INITIAL,
                        ]);
                } else {
                    AnalyticStat::query()
                        ->create([
                            'group_id' => $group_id,
                            'date' => $date,
                            'row_id' => $row->id,
                            'column_id' => $column->id,
                            'value' => '',
                            'show_value' => '',
                            'editable' => 1,
                            'class' => 'text-left font-bold',
                            'type' => AnalyticStat::INITIAL,
                        ]);
                }
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

            $date = Carbon::createFromDate($dto->year, $dto->month);
            $firstDayOfMonth = $date->firstOfMonth()->toDateString();

            $fields = ['name', 'plan', 'sum', 'avg'];
            $columns = AnalyticColumn::query()->where([
                'group_id' => $dto->groupId,
                'date' => $firstDayOfMonth
            ])->get();

            /**
             * Создать первую строку.
             */
            $row = self::query()->create([
                'group_id' => $dto->groupId,
                'name' => $dto->rows['name'] ?? '',
                'date' => $firstDayOfMonth,
                'order' => 1,
            ]);

            /**
             * Создаем в таблице аналитики данные.
             */
            $stats = $columns->whereIn('name', $fields)->map(function ($column, $index) use (
                $row,
                $dto,
                $date
            ) {
                return [
                    'group_id' => $dto->groupId,
                    'date' => $date->firstOfMonth()->toDateString(),
                    'row_id' => $row->id,
                    'column_id' => $column->id,
                    'value' => $column->name,
                    'show_value' => $index == 0 ? $dto->rows['name'] : $column->name,
                    'editable' => 1,
                    'class' => 'text-center font-bold bg-grey',
                    'type' => AnalyticStat::INITIAL,
                ];
            })->toArray();

            for ($day = 1; $day <= $date->daysInMonth; $day++) {
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
