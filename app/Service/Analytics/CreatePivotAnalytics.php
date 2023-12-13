<?php

namespace App\Service\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Repositories\ProfileGroupRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
            $this->createStats($group->id);
        }
    }

    private function createStats(int $group_id): void
    {
        $prevDate = $this->previousMonth();
        $currentDate = $this->currentMonth();

        DB::table('analytic_stats')
            ->where('date', $currentDate)
            ->where('group_id', $group_id)
            ->delete();

        $newRows = $this->createRows($group_id);
        $newCols = $this->createColumns($group_id);

        $colsWithValue = $this->getColsWithValue($currentDate, $group_id);

        $prevMonthStats = AnalyticStat::query()
            ->where('date', $prevDate)
            ->where('group_id', $group_id)
            ->get();

        $lastColumnId = 0;

        foreach ($prevMonthStats as $statistic) {
            if ($statistic->show_value != '' || !is_numeric($statistic->show_value)) {
                $exists = AnalyticStat::query()
                    ->where('group_id', $group_id)
                    ->where('date', $currentDate)
                    ->where('show_value', $statistic->show_value)
                    ->exists();
                dump($statistic->show_value . ' : ' . ($exists ? 'create' : 'skip'));
                if ($exists) continue;
            }

            $value = $this->getValue($statistic, $newRows, $newCols, $colsWithValue);
            $show_value = $this->getShowValue($statistic, $newRows, $newCols, $colsWithValue);
            $lastColumnId = $newCols[$statistic->column_id];

            $newStat = $statistic->replicate();
            $newStat->date = $currentDate;
            $newStat->row_id = $newRows[$statistic->row_id];
            $newStat->column_id = $newCols[$statistic->column_id];
            $newStat->value = $value;
            $newStat->show_value = $show_value;
            $newStat->save();
        }

        $lastColumnStats = AnalyticStat::query()
            ->where('column_id', $lastColumnId)
            ->where('group_id', $group_id)
            ->where('date', $currentDate)
            ->get();

        /**
         * Скрипт запускается если дни текущего месяца больше чем прошлый.
         */
        foreach ($this->monthDifference() as $diffDay) {
            foreach ($lastColumnStats as $key => $columnStat) {
                AnalyticStat::query()->updateOrCreate(
                    [
                        'group_id' => $columnStat->group_id,
                        'date' => $currentDate,
                        'show_value' => $key == 0 ? $diffDay : ''],
                    [
                        'row_id' => $columnStat->row_id,
                        'column_id' => ++$columnStat->column_id,
                        'value' => '',
                        'activity_id' => null,
                        'editable' => $columnStat->editable,
                        'class' => $columnStat->class,
                        'type' => $columnStat->type,
                        'comment' => $columnStat->comment,
                        'decimals' => $columnStat->decimals
                    ]);
            }
        }
    }

    private function createRows(int $group_id): array
    {
        $prevDate = $this->previousMonth();
        $currentDate = $this->currentMonth();

        DB::table('analytic_rows')
            ->where('date', $currentDate)
            ->where('group_id', $group_id)
            ->delete();

        $newRows = [];

        /** @var Collection<AnalyticRow> $prevRows */
        $prevRows = AnalyticRow::query()
            ->where('date', $prevDate)
            ->where('group_id', $group_id)
            ->orderBy('order', 'desc')
            ->get();

        foreach ($prevRows as $prevRow) {

            if ($prevRow->name !== 'name') {
                $exists = AnalyticRow::query()
                    ->where('group_id', $group_id)
                    ->where('date', $currentDate)
                    ->where('name', $prevRow->name)
                    ->exists();
                dump($prevRow->name . ' : ' . ($exists ? 'create' : 'skip'));
                if ($exists) continue;
            }

            $newRow = $prevRow->replicate();
            $newRow->date = $currentDate;

            $newRow->save();
            $newRows[$prevRow->id] = $newRow->getKey();
        }
        /**
         * depend rows
         */
        $rows = AnalyticRow::query()
            ->where('date', $currentDate)
            ->where('group_id', $group_id)
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

    private function createColumns(int $group_id): array
    {
        $prevDate = $this->previousMonth();
        $currentDate = $this->currentMonth();

        DB::table('analytic_columns')
            ->where('date', $currentDate)
            ->where('group_id', $group_id)
            ->delete();

        /**
         * Получаем данные за прошлый месяц.
         */
        $prevMonthCols = AnalyticColumn::query()
            ->where([
                'date' => $prevDate,
                'group_id' => $group_id
            ])
            ->whereIn('name', $this->getMonthlyTemplate($currentDate))
            ->orderBy('order')
            ->get();

        $newColumns = [];
        $lastOrder = 0;
        foreach ($prevMonthCols as $col) {
            $exists = AnalyticColumn::query()
                ->where('group_id', $group_id)
                ->where('date', $currentDate)
                ->where('name', $col->name)
                ->exists();
            dump($col->name . ' : ' . ($exists ? 'create' : 'skip'));
            if ($exists) continue;

            $newColumn = $col->replicate();
            $newColumn->date = $currentDate;
            $newColumn->save();

            $lastOrder = $col->order;

            /**
             * Сохраняем ID новой колонки в массиве.
             */
            $newColumns[$col->id] = $newColumn->getKey();
        }

        foreach ($this->monthDifference() as $diffDay) {
            $newColumn = AnalyticColumn::query()->firstOrCreate([
                'group_id' => $group_id,
                'name' => (string)$diffDay,
                'date' => $currentDate
            ],
                [
                    'order' => ++$lastOrder,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            /**
             * Сохраняем ID новой колонки в массиве.
             */
            $newColumns[$newColumn->getKey()] = $newColumn->getKey();
        }
        return $newColumns;
    }

    private function getColsWithValue($date, $group_id): array
    {
        return AnalyticColumn::query()
            ->where([
                'date' => $date,
                'group_id' => $group_id
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