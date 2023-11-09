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
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\TopValue as ValueModel;
use App\Models\Analytics\UserStat;
use App\Models\WorkChart\WorkChartModel;
use App\ProfileGroup;
use App\Repositories\ActivityRepository;
use App\Repositories\Analytics\AnalyticColumnRepository;
use App\Repositories\Analytics\AnalyticRowRepository;
use App\Repositories\Analytics\AnalyticStatRepository;
use App\Timetracking;
use App\Traits\AnalyticTrait;
use App\WorkingDay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class Analytics
{
    use AnalyticTrait;

    const VALUE_PLAN = 'plan';
    const VALUE_IMPL = 'Impl';

    public function __construct(
        private readonly AnalyticRowRepository    $rowRepository,
        private readonly AnalyticColumnRepository $columnRepository,
        private readonly AnalyticStatRepository   $statRepository,
        private readonly ActivityRepository       $activityRepository,
    )
    {
    }

    public static function getArr(
        AnalyticStat    $statistic,
        AnalyticRow     $row,
        AnalyticColumn  $column,
        string          $cellLetter,
        int|string|null $cellNumber,
        string          $addClass,
        int             $editable
    ): array
    {
        return [
            'value' => $statistic->value,
            'show_value' => $statistic->show_value,
            'context' => false,
            'type' => $statistic->type,
            'row_id' => $row->id,
            'column_id' => $column->id,
            'cell' => $cellLetter . $cellNumber,
            'class' => $statistic->class . $addClass,
            'editable' => $editable == 0 ? 0 : $statistic->editable,
            'depend_id' => $row->depend_id,
            'decimals' => $statistic->decimals,
            'comment' => $statistic->comment,
            'sign' => ''
        ];
    }

    public static function getClass(
        string       $name,
        array        $weekdays,
        ?AnalyticRow $depending_from_row
    ): string
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
        $columns = $this->columnRepository->getByGroupId($dto->groupId, $date);
        $stats = $this->statRepository->getByGroupId($dto->groupId, $date);

        $activities = $this->activityRepository->getByGroupIdWithTrashed($dto->groupId);

        $keys = $this->getKeys($rows, $columns);
        $weekdays = AnalyticStat::getWeekdays($date);

        $table = [];
        foreach ($rows as $rowIndex => $row) {
            $item = [];
            $dependingFromRow = $rows->where('depend_id', $row->id)->first();
            $cellNumber = $rowIndex + 1;

            foreach ($columns as $columnIndex => $column) {

                $addClass = self::getClass($column->name, $weekdays, $dependingFromRow);
                $cellLetter = $columnIndex != 0 ? AnalyticStat::getLetter($columnIndex - 1) : 'A';
                /** @var AnalyticStat $statistic */
                $statistic = $stats
                    ->where('row_id', $row->id)
                    ->where('column_id', $column->id)
                    ->first();
//                $arr = [
//                    'row_id' => $row->id,
//                    'column_id' => $column->id,
//                    'context' => false,
//                    'cell' => $cellLetter . $cellNumber,
//                    'depend_id' => $row->depend_id,
//                ];

                if ($statistic) {
                    $arr = self::getArr($statistic, $row, $column, $cellLetter, $cellNumber, $addClass, $rowIndex);
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
                        $val = '';
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
                        $groupSalary = GroupSalary::query()
                            ->where('group_id', $dto->groupId)
                            ->where('date', $date)
                            ->get()
                            ->sum('total');
                        $val = floor($groupSalary);
                        $statistic->show_value = $val;
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'salary_day' && !in_array($column->name, ['plan', 'sum', 'avg', 'name'])) {
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
                }
                else {
                    $type = 'initial';

                    if ($column->name == 'sum' && $rowIndex > 3) {
                        $type = 'sum';
                    }

                    if ($column->name == 'avg' && $rowIndex > 3) {
                        $type = 'avg';
                    }

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
        \Illuminate\Database\Eloquent\Collection $columns,
        Collection                               $stats,
        int                                      $rowId,
        array                                    $days = []
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

    public function getKeys(Collection|array $rows, Collection|array $columns): array
    {
        $rowKeys = $rows->mapWithKeys(function ($row, $index) {
            return [$row->id => $index + 1];
        })->toArray();

        $columnKeys = $columns->mapWithKeys(function ($column, $index) {
            return [$column->id => $index + 1];
        })->toArray();


        return [
            'rows' => $rowKeys,
            'columns' => $columnKeys
        ];
    }

    public function userStatisticFormTable(
        Activity $activity,
        string   $date,
        int      $groupId = null
    ): Collection
    {
        $group = ProfileGroup::query()->where('id', $groupId)->first();
        $dateFrom = Carbon::createFromDate($date)->endOfMonth()->format('Y-m-d');
        $firstOfMonth = Carbon::createFromDate($date)->firstOfMonth()->format('Y-m-d');
        $dateTo = Carbon::createFromDate($date)->addMonth()->startOfMonth()->format('Y-m-d');

        $employees = $group->actualAndFiredEmployees($dateFrom, $dateTo);

        return $employees
            ->with('statistics', fn($statistic) => $statistic->select([
                DB::raw('DAY(date) as day'),
                'user_id',
                'value',
                'date'
            ])->where('activity_id', $activity->id)->where('date', '>=', $firstOfMonth)->where('date', '<=', $dateFrom))
            ->get()
            ->map(function ($employee) use ($date, $activity) {
                $workDay = isset($user->working_day_id) && $user->working_day_id == 1 ? WorkingDay::FIVE_DAYS : WorkingDay::SIX_DAYS;
                $appliedFrom = $employee->workdays_from_applied($date, $workDay);
                $workDays = WorkChartModel::workdaysPerMonth($employee);


                $employee->fullname = $employee->full_name;
                $employee->fired = $employee->deleted_at != null ? 1 : 0;
                $employee->applied_from = $appliedFrom;
                $employee->is_trainee = 1;
                $employee->plan = $activity->daily_plan * $workDays;


                return $employee;
            });
    }

    public function decompositionTable(
        GetAnalyticDto $dto
    ): array
    {
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);
        $decompositions = DecompositionValue::query()->where([
            'group_id' => $dto->groupId,
            'date' => $date,
        ])->get();


        return [
            'group_id' => $dto->groupId,
            'records' => $decompositions
        ];
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

    public function rentability(
        UtilityDto $dto
    ): array
    {
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);
        $group = ProfileGroup::whereIn('id', $dto->groupIds)->first();

        $topValue = new ValueModel;
        $options = $topValue->getOptions('[]');
        $options['staticZones'] = $this->getStaticZones($group);

        $options['staticLabels']['labels'] = $this->labels($group);

        return [
            'name' => $group->name ?? 'Рентабельность',
            'value' => (float)$this->getRentabilityValue($group->id, $date),
            'group_id' => $group->id,
            'place' => 1,
            'unit' => '%',
            'editable' => false,
            'edit_value' => false,
            'activity_id' => 0,
            'key' => 999 * 1000,
            'min_value' => 0,
            'max_value' => $group->rentability_max,
            'round' => 2,
            'cell' => '',
            'is_main' => 0,
            'fixed' => 0,
            'value_type' => 'sum',
            'sections' => $options['staticLabels']['labels'],
            'options' => $options,
            'diff' => $this->rentabilityDiff($group->id, $date)
        ];
    }

    public function activitiesViews(
        int   $groupId,
        array $views
    ): Collection
    {
        return Activity::query()
            ->where('group_id', $groupId)
            ->whereIn('view', $views)
            ->orderByDesc('order')->get();
    }

    private function analyticTableValue(
        Collection $rowsData,
        Collection $columnsData
    ): array
    {
        $rows = [];
        $columns = [];

        foreach ($rowsData as $index => $row) {
            $rows[$row->id] = $index + 1;
        }

        foreach ($columnsData as $index => $column) {
            $columns[$column->id] = $index != 0 ? AnalyticStat::getLetter($index - 1) : 'A';
        }

        return [
            'rows' => $rows,
            'columns' => $columns
        ];
    }

    private function rentabilityDiff(
        int    $groupId,
        string $date
    ): float|int
    {
        $currentMonthImpl = $this->rentabilityByDay($groupId, $date);
        $prevMonthImpl = $this->rentabilityByDay($groupId, $date);

        return round($currentMonthImpl - $prevMonthImpl, 2);
    }

    private function rentabilityByDay(
        int    $groupId,
        string $date
    ): float|int
    {
        $impl = 0;
        $days = $this->getDaysPerMonth($date);
        $stat = $this->implStat($groupId, $date) ?? null;

        return $stat ? AnalyticStat::calcFormula($stat, $date, 2, $days) : $impl;
    }

    private function implStat(
        int    $groupId,
        string $date
    ): Model|null
    {
        $implStat = null;

        $column = AnalyticColumn::query()
            ->where('date', $date)
            ->where('name', self::VALUE_PLAN)->first() ?? null;

        $row = AnalyticRow::query()
            ->where('date', $date)
            ->where('name', self::VALUE_IMPL)->first() ?? null;

        if ($row && $column) {
            $implStat = AnalyticStat::query()
                ->where('column_id', $column->id)
                ->where('row_id', $row->id)->first();
        }

        return $implStat;
    }

    private function getDaysPerMonth(
        string $date
    ): array
    {
        $date = Carbon::createFromDate($date)->daysInMonth;
        $days = [];

        for ($day = 1; $day <= $date; $day++) {
            $days[] = $day;
        }

        return $days;
    }

    private function getRentabilityValue($group_id, $date): float|int
    {
        $val = 0;

        $column = $this->getGroupPlanColumns($group_id, $date)->first() ?? [];
        $row = $this->getGroupImplRows($group_id, $date)->first() ?? [];

        if ($row && $column) {
            $stat = AnalyticStat::query()->where('column_id', $column->id)
                ->where('row_id', $row->id)
                ->where('date', $date)
                ->first();
            if ($stat) {
                $val = AnalyticStat::calcFormula($stat, $date, 2);
                $stat->show_value = $val;
                $stat->save();
            }
        }

        return $val;
    }

    private function getGroupPlanColumns($group_id, $date): \Illuminate\Database\Eloquent\Builder
    {
        return AnalyticColumn::query()
            ->where('date', $date)
            ->where('name', 'plan');
    }

    private function getGroupImplRows($group_id, $date): \Illuminate\Database\Eloquent\Builder|null
    {
        return AnalyticRow::query()
            ->where('date', $date)
            ->where('name', 'Impl');
    }

    private function getStaticZones(?ProfileGroup $group): array
    {
        return [
            ['strokeStyle' => "#F03E3E", 'min' => 0, 'max' => 49], // Red
            ['strokeStyle' => "#fd7e14", 'min' => 50, 'max' => 74], // Orange
            ['strokeStyle' => "#FFDD00", 'min' => 75, 'max' => 99], // Yellow
            ['strokeStyle' => "#30B32D", 'min' => 100, 'max' => $group->rentability_max], // Green
        ];
    }

    private function labels(?ProfileGroup $group): array
    {
        return [0, 50, 100, $group->rentability_max];
    }
}