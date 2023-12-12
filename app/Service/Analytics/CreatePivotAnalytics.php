<?php

namespace App\Service\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Repositories\ProfileGroupRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreatePivotAnalytics implements CreatePivotAnalyticsInterface
{
    public function __construct(
        private readonly ProfileGroupRepository $repository
    )
    {
    }

    public function create(): void
    {
        $groups = $this->repository->getActiveGroupsWhereHasAnalytics();
        foreach ($groups as $group) {
            $this->createStatistics($group->getKey());
        }
    }

    private function createStatistics(int $groupId): void
    {
        $prevMonth = $this->previousMonth();
        $currentMonth = $this->currentMonth();

        $newRows = $this->copyRows($groupId);
        $newCols = $this->copyCols($groupId);

        $colsWithValue = $this->getColsWithValue($currentMonth, $groupId);

        $prevMonthStatistics = AnalyticStat::query()
            ->where([
                'date' => $prevMonth,
                'group_id' => $groupId
            ])
            ->get();
        $lastColumnId = 0;

        foreach ($prevMonthStatistics as $statistic) {

            $existsRowAndCol = array_key_exists($statistic->row_id, $newRows)
                && array_key_exists($statistic->column_id, $newCols);

            if (!$existsRowAndCol) continue;

            $value = $this->getValue($statistic, $newRows, $newCols, $colsWithValue);
            $show_value = $this->getShowValue($statistic, $newRows, $newCols, $colsWithValue);
            $lastColumnId = $newCols[$statistic->column_id];
            AnalyticStat::query()->firstOrCreate([
                'group_id' => $statistic->group_id,
                'date' => $currentMonth,
                'row_id' => $newRows[$statistic->row_id],
                'column_id' => $newCols[$statistic->column_id],
            ], [
                'value' => $value,
                'show_value' => $show_value,
                'activity_id' => $statistic->activity_id,
                'editable' => $statistic->editable,
                'class' => $statistic->class,
                'type' => $statistic->type,
                'comment' => $statistic->comment,
                'decimals' => $statistic->decimals,
            ]);
        }
        $lastColumnStats = AnalyticStat::query()
            ->where('column_id', $lastColumnId)
            ->where('group_id', $groupId)
            ->where('date', $currentMonth)
            ->get();

        $analyticStats = [];

        /**
         * Скрипт запускается если дни текущего месяца больше чем прошлый.
         */
        foreach ($this->monthDifference() as $diffDay) {
            foreach ($lastColumnStats as $key => $columnStat) {
                $analyticStats[] = [
                    'group_id' => $columnStat->group_id,
                    'date' => $currentMonth,
                    'row_id' => $columnStat->row_id,
                    'column_id' => ++$columnStat->column_id,
                    'value' => '',
                    'show_value' => $key == 0 ? $diffDay : '',
                    'activity_id' => null,
                    'editable' => $columnStat->editable,
                    'class' => $columnStat->class,
                    'type' => $columnStat->type,
                    'comment' => $columnStat->comment,
                    'decimals' => $columnStat->decimals
                ];
            }
        }

        DB::table('analytic_stats')->insert($analyticStats);
    }

    private function copyRows(int $groupId): array
    {
        $prevMonth = $this->previousMonth();
        $currentMonth = $this->currentMonth();

        $newRows = [];
        $prevRows = AnalyticRow::query()
            ->where([
                'date' => $prevMonth,
                'group_id' => 136
            ])
            ->orderBy('order', 'desc')
            ->get();
        foreach ($prevRows as $prevRow) {
            $newRow = AnalyticRow::query()
                ->firstOrCreate([
                    'group_id' => $prevRow->group_id,
                    'name' => $prevRow->name,
                    'date' => $currentMonth,
                ], [
                    'order' => $prevRow->order,
                    'depend_id' => $prevRow->depend_id,
                ]);
            $newRows[$prevRow->id] = $newRow->getKey();
        }
        /**
         * depend rows
         */
        $rows = AnalyticRow::query()
            ->where('date', $currentMonth)
            ->where('group_id', $groupId)
            ->whereNotNull('depend_id')
            ->orderBy('order', 'desc')
            ->get();

        foreach ($rows as $row) {
            $row->depend_id = in_array($row->id, $newRows)
            && array_key_exists($row->depend_id, $newRows)
                ? $newRows[$row->depend_id]
                : null;
            $row->save();
        }

        return $newRows;
    }

    private function copyCols(int $groupId): array
    {
        $prevMonth = $this->previousMonth();
        $currentMonth = $this->currentMonth();


        $prevMonthCols = AnalyticColumn::query()
            ->where([
                'date' => $prevMonth,
                'group_id' => $groupId
            ])
            ->whereIn('name', $this->getMonthlyTemplate($currentMonth))
            ->orderBy('order')
            ->get();

        $newColumns = [];
        $lastOrder = 0;
        foreach ($prevMonthCols as $col) {
            $analyticColumn = AnalyticColumn::query()->firstOrCreate([
                'group_id' => $col->group_id,
                'name' => $col->name,
                'date' => $currentMonth,
                'order' => $col->order,
            ]);

            /**
             * Получаем последний элемент в массиве.
             * После последний итерации останется последний элемент
             */
            $lastOrder = $col->order;

            /**
             * Сохраняем ID новой колонки в массиве.
             */
            $newColumns[$col->id] = $analyticColumn->getKey();
        }
        foreach ($this->monthDifference() as $diffDay) {
            $newColumn = AnalyticColumn::query()->firstOrCreate([
                'group_id' => $groupId,
                'name' => (string)$diffDay,
                'date' => $currentMonth
            ],
                [
                    'order' => ++$lastOrder,
                ]);

            /**
             * Сохраняем ID новой колонки в массиве.
             */
            $newColumns[$newColumn->getKey()] = $newColumn->getKey();
        }
        return $newColumns;
    }

    private function getColsWithValue($date, $groupId): array
    {
        return AnalyticColumn::query()
            ->where([
                'date' => $date,
                'group_id' => $groupId
            ])
            ->whereIn('name', [
                'name',
                'plan',
                'sum',
                'avg',
            ])
            ->get('id')
            ->pluck('id')
            ->toArray();
    }

    private function getValue(
        AnalyticStat $statistic,
        array        $newRows,
        array        $newCols,
        array        $colsWithValue
    ): string|int|null
    {
        $value = $statistic->value;
        if ($statistic->type == 'remote' || $statistic->type == 'inhouse') {
            $value = '';
        }

        if ($statistic->type == 'initial' || in_array($newCols[$statistic->column_id], $colsWithValue)) {
            $value = '';
        }

        if ($statistic->type == 'formula') {
            $value = AnalyticStat::convert_formula_to_new_month($statistic->value, $newRows, $newCols);
        }

        if ($statistic->row_id == array_values($newRows)[0]) {
            $value = $statistic->value;
        }

        return $value;
    }

    private function getShowValue(
        AnalyticStat $statistic,
        array        $newRows,
        array        $newCols,
        array        $colsWithValue
    ): string|int|null
    {
        $value = '';
        $row_id = $newRows[$statistic->row_id];

        if ($row_id == array_values($newRows)[0]
            || $statistic->type == 'formula') {
            $value = $statistic->show_value;
        }

        if (in_array($newCols[$statistic->column_id], $colsWithValue)) {
            $value = $statistic->show_value;
        }

        if (in_array($statistic->type, ['formula', 'avg', 'sum', 'salary'])) {
            $value = '';
        }

        return $value;
    }

    private function currentMonth(): string
    {
        return Carbon::now()
            ->startOfMonth()
            ->format('Y-m-d');
    }

    private function previousMonth(): string
    {
        return Carbon::now()
            ->subMonth()
            ->startOfMonth()
            ->format('Y-m-d');
    }

    private function monthDifference(): array
    {
        $daysInCurrentMonth = Carbon::parse($this->currentMonth())
            ->daysInMonth;
        $daysInPrevMonth = Carbon::parse($this->previousMonth())
            ->daysInMonth;

        return array_diff(
            $this->monthDaysInArray($daysInCurrentMonth),
            $this->monthDaysInArray($daysInPrevMonth)
        );
    }

    private function monthDaysInArray(int $daysInMonth): array
    {
        $days = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $days[] = $day;
        }

        return $days;
    }

    private function getMonthlyTemplate(string $date): array
    {
        /**
         * Добавляем 4 потому что есть колонки name, plan, avg, sum и дни в месяце.
         */
        $nameColumn = ['name', 'plan', 'sum', 'avg'];
        $daysInMonth = Carbon::parse($date)->daysInMonth;

        for ($column = 1; $column <= $daysInMonth; $column++) {
            $nameColumn[] = $column;
        }

        return $nameColumn;
    }
}