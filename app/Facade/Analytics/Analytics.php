<?php

namespace App\Facade\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\DTO\Analytics\V2\UtilityDto;
use App\GroupSalary;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\TopValue as ValueModel;
use App\Models\Analytics\UserStat;
use App\ProfileGroup;
use App\Repositories\Analytics\AnalyticColumnRepository;
use App\Repositories\Analytics\AnalyticRowRepository;
use App\Repositories\Analytics\AnalyticStatRepository;
use App\Timetracking;
use App\Traits\AnalyticTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class Analytics
{
    use AnalyticTrait;

    public function __construct(
        private readonly AnalyticRowRepository    $rowRepository,
        private readonly AnalyticColumnRepository $columnRepository,
        private readonly AnalyticStatRepository   $statRepository,
    )
    {
    }

    public static function getClass(string $name, ?AnalyticRow $depending_from_row, array $weekdays): string
    {
        if (!in_array((int)$name, $weekdays) && !in_array($name, ['plan', 'sum', 'avg', 'name'])) { // weekday coloring
            $add_class = ' weekday';
        } else {
            $add_class = '';
        }

        if (!in_array($name, ['sum', 'avg', 'name'])) {
            $add_class .= ' text-center';
        }

        if ($depending_from_row) {
            $add_class .= ' bg-violet';
        }
        return $add_class;
    }

    public function analytics(GetAnalyticDto $dto): array
    {
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);

        $rows = $this->rowRepository->getByGroupId($dto->groupId, $date);

        $columns = AnalyticColumn::query()
            ->where('date', $date)
            ->where('group_id', $dto->groupId)
            ->orderBy('order')
            ->get();
        $stats = AnalyticStat::with('activity')
            ->where('date', $date)
            ->where('group_id', $dto->groupId)
            ->get();

        $activities = Activity::withTrashed()->where('group_id', $dto->groupId)->get();

        $keys = $this->getKeys($rows, $columns);


        $weekdays = AnalyticStat::getWeekdays($date);
        $table = [];

        foreach ($rows as $rowIndex => $row) {
            $item = [];
            $dependingFromRow = $rows->where('depend_id', $row->id)->first();
            $cellNumber = $rowIndex + 1;


            foreach ($columns as $columnIndex => $column) {

                $addClass = self::getClass($column->name, $dependingFromRow, $weekdays);

                $cellLetter = $columnIndex != 0 ? AnalyticStat::getLetter($columnIndex - 1) : 'A';

                $statistic = $stats->where('row_id', $row->id)->where('column_id', $column->id)->first();

                $arr = [
                    'row_id' => $row->id,
                    'column_id' => $column->id,
                    'context' => false,
                    'cell' => $cellLetter . $cellNumber,
                    'depend_id' => $row->depend_id,
                ];
                if ($statistic) {
                    if ($statistic->activity_id != null) {
                        $act = $activities->where('id', $statistic->activity_id)->first();
                        if ($act && $act->unit) {
                            $arr['sign'] = $act->unit;
                        }
                    }

                    if ($statistic->type == 'formula') {
                        $val = AnalyticStat::calcFormula($statistic, $date, $statistic->decimals);
                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = AnalyticStat::convert_formula($statistic->value, $keys['rows'], $keys['columns']);
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'stat') {
                        $day = Carbon::parse($date)->day($column->name)->format('Y-m-d');
                        if ($statistic->activity) {
                            // TODO: here should be condition, $statistic->activity can be null
                            $val = $this->totalForDay($statistic->activity, $day);
                        }
                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'sum') {
                        $val = $this->daysSum($columns, $stats, $row->id);
                        $val = round($val, 1);
                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'avg') {
                        $val = $this->daysAvg($columns, $stats, $row->id);
                        $statistic->show_value = round($val, 1);
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'salary') {
                        $groupSalary = GroupSalary::where('group_id', $dto->groupId)->where('date', $date)->get()->sum('total');
                        $val = floor($groupSalary);
                        $statistic->show_value = $val;
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'salary_day' && !in_array($column->name, ['plan', 'sum', 'avg', 'name'])) {
                        //$groupSalary = 0;
                        $val = 0;
                        $statistic->show_value = $val;
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'time') {
                        $day = Carbon::parse($date)->day($column->name)->format('Y-m-d');

                        $val = Timetracking::totalHours($day, $dto->groupId);
                        $val = floor($val / 9 * 10) / 10;
                        $val = max($val, 0);

                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = round($val, 1);
                        $arr['show_value'] = round($val, 1);
                    }

                    $arr = [
                        'value' => $statistic->value,
                        'show_value' => $statistic->show_value,
                        'context' => false,
                        'type' => $statistic->type,
                        'row_id' => $row->id,
                        'column_id' => $column->id,
                        'cell' => $cellLetter . $cellNumber,
                        'class' => $statistic->class . $addClass,
                        'editable' => $rowIndex == 0 ? 0 : $statistic->editable,
                        'depend_id' => $row->depend_id,
                        'decimals' => $statistic->decimals,
                        'comment' => $statistic->comment,
                        'sign' => ''
                    ];

                } else {
                    $type = $column->name == 'sum' && $rowIndex > 3 ? 'sum' : 'initial';
                    $type = $column->name == 'avg' && $rowIndex > 3 ? 'avg' : 'initial';

                    AnalyticStat::query()->create([
                        'group_id' => $dto->groupId,
                        'date' => $date,
                        'row_id' => $row->id,
                        'column_id' => $column->id,
                        'value' => '',
                        'show_value' => '',
                        'decimals' => 0,
                        'type' => $type,
                        'class' => 'text-center' . $addClass,
                        'editable' => $rowIndex == 0 ? 0 : 1,
                    ]);
                    $arr = [
                        'value' => '',
                        'show_value' => '',
                        'context' => false,
                        'row_id' => $row->id,
                        'column_id' => $column->id,
                        'decimals' => 0,
                        'type' => $type,
                        'cell' => $cellLetter . $cellNumber,
                        'class' => 'text-center' . $addClass,
                        'editable' => $rowIndex == 0 ? 0 : 1,
                        'depend_id' => $row->depend_id,
                        'comment' => '',
                        'sign' => '',
                    ];
                }

                $item[$column->name] = $arr;
            }

            $table[] = $item;
        }

        return $table;
    }

    private function totalForDay(Activity $activity, $date): int|float
    {
        $method = ($activity->plan_unit === 'minutes' || $activity->plan_unit === 'less_sum') ? 'sum' : 'avg';

        $total = UserStat::query()->where('activity_id', $activity->id)
            ->where('date', $date)
            ->where('value', '>', 0)
            ->{$method}('value');

        if ($method === 'avg') {
            $total = round($total, 1);
        }

        return $total;
    }

    public function daysSum(
        Collection $columns,
        Collection $stats,
        int        $rowId,
        array      $days = []
    ): float|int
    {

        $days = empty($days) ? range(1, 31) : $days;

        $columns = $columns->whereIn('name', $days)->pluck('id')->toArray();

        $total = 0;

        $stats = $stats->where('row_id', $rowId)->whereIn('column_id', $columns);

        foreach ($stats as $stat) {
            if ($stat && is_numeric($stat->show_value)) {
                $total += (float)$stat->show_value;
            }
        }

        return $total;
    }

    public function daysAvg(
        Collection $columns,
        Collection $stats,
        int        $rowId,
        array      $days = []
    ): float|int
    {
        $days = empty($days) ? range(1, 31) : $days;

        $columns = $columns->whereIn('name', $days)->pluck('id')->toArray();

        $total = 0;
        $count = 0;

        $stats = $stats->where('row_id', $rowId)->whereIn('column_id', $columns);

        foreach ($stats as $stat) {
            $total += (float)$stat->show_value;
            if ((float)$stat->show_value != 0) {
                $count++;
            }
        }

        if ($count > 0) {
            $total = round($total / $count, 3);
        } else {
            $total = 0;
        }

        return $total;
    }


    public function utility(
        UtilityDto $dto
    ): array
    {
        $groupIds = $dto->groupIds ?? ProfileGroup::profileGroupsWithArchived($dto->year, $dto->month, false, false, ProfileGroup::SWITCH_UTILITY);
        $groups = ProfileGroup::query()->whereIn('id', $groupIds)->get();
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);
        $gauges = [];
        foreach ($groups as $group) {
            $percent = 0;
            $tops = ValueModel::getByGroupAndDate($group->id, $date)->get()->map(
                function ($top) use ($group, $date, $percent) {
                    return [
                            'id' => $top->id,
                            'group_id' => $top->group_id,
                            'place' => 1,
                            'activity_id' => $top->activity_id,
                            'unit' => $top->unit,
                            'editable' => false,
                            'edit_value' => false,
                            'diff' => 0,
                            'cell' => $top->cell,
                            'round' => $top->round,
                            'fixed' => $top->fixed,
                            'is_main' => $top->is_main,
                            'value_type' => $top->value_type,
                            'key' => $top->id * 1000,
                        ] + ValueModel::getDynamicValue($group->id, $date, $top);
                });

            $gauges[] = [
                'group_id' => $group->id,
                'name' => $group->name,
                'gauges' => $tops,
                'group_activities' => Activity::query()->where('group_id', $group->id)->get(),
                'archive_utility' => $group->archive_utility,
            ];
        }

        return $gauges;
    }

    public function getKeys(Collection|array $rows, Collection|array $columns): array
    {
        $rowKeys = $rows->mapWithKeys(function ($row, $index) {
            return [$index + 1 => $row->id];
        })->toArray();

        $columnKeys = $columns->mapWithKeys(function ($column, $index) {
            return [$index + 1 => $column->id];
        })->toArray();


        return [
            'rows' => $rowKeys,
            'columns' => $columnKeys
        ];
    }
}