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
use App\Salary;
use App\Timetracking;
use App\Traits\AnalyticTrait;
use App\WorkingDay;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function convertCellFormulaToCoordinates(AnalyticStat $stat, string $value, string $formula): string
    {
        $date = $stat->date;
        $coordinates = $this->getCoordinates($stat->group_id, $date, $value);
//        dd($formula, $coordinates, Str::contains($formula, $coordinates));
        if (!$coordinates) return $formula;
        if (Str::contains($formula, $coordinates)) return $formula;
        return $coordinates . $formula;
    }

    public function getCoordinates(int $group_id, string $date, ?string $cell = null): ?string
    {
        // get indexes
        $r_index = 0;
        $c_index = 0;

        $column_letters = '';
        $row_letters = '';

        $matches = [];
        preg_match_all('/[a-zA-Z]{1,2}+/', $cell, $matches);

        if (count($matches[0]) > 0) {
            $column_letters = strtoupper($matches[0][0]);
        }

        $matches = [];
        preg_match_all('/\d+/', $cell, $matches);

        if (count($matches[0]) > 0) {
            $row_letters = $matches[0][0];
        }

        if ($column_letters != '') {
            $i = 0;
            if ($column_letters == 'A') {
                $c_index = 0;
            } else {
                while ($column_letters != AnalyticStat::getLetter($i)) {
                    $i++;
                }
                $c_index = $i + 1;
            }
        }

        if ($row_letters != '') {
            $r_index = (int)$row_letters - 1;
        }

        $columns = AnalyticColumn::query()
            ->where('date', $date)
            ->where('group_id', $group_id)
            ->orderBy('order')
            ->get();

        $column = $columns->toArray()[$c_index] ?? null;

        // get row
        $rows = AnalyticRow::query()
            ->where('date', $date)
            ->where('group_id', $group_id)
            ->orderBy('order', 'desc')
            ->get();

        $row = $rows->toArray()[$r_index] ?? null;

        if (!$row || !$column) {
            return null;
        }

        return '[' . $column['id'] . ':' . $row['id'] . ']';
    }

    /**
     * @throws Exception
     */
    public function analytics(GetAnalyticDto $dto): array
    {
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);

        $rows = $this->rowRepository->getByGroupId($dto->groupId, $date);
        $columns = $this->columnRepository
            ->getByGroupId($dto->groupId, $date);
        $stats = $this->statRepository->getByGroupId($dto->groupId, $date);

        $activities = $this->activityRepository->getByGroupIdWithTrashed($dto->groupId);
        $fot = Salary::getSalaryForDays(['date' => $date, 'group_id' => $dto->groupId]);
        $keys = $this->getKeys($rows, $columns);
        $weekdays = AnalyticStat::getWeekdays($date);

        $table = [];

        $days = range(1, Carbon::parse($date)->lastOfMonth()->day);
        $columnIds = $columns->whereIn('name', $days)->pluck('id')->toArray();

        foreach ($rows as $rowIndex => $row) {
            $item = [];
            $dependingFromRow = $rows->where('depend_id', $row->id)->first();
            $cellNumber = $rowIndex + 1;

            foreach ($columns as $columnIndex => $column) {
                $addClass = self::getClass($column->name, $weekdays, $dependingFromRow);
                $cellLetter = AnalyticStat::getLetter($columnIndex);
                /** @var AnalyticStat $statistic */
                $statistic = $stats
                    ->where('row_id', $row->id)
                    ->where('column_id', $column->id)
                    ->first();

                if ($statistic) {
                    $arr = self::getArr($statistic, $row, $column, $cellLetter, $cellNumber, $addClass, $rowIndex);
                    if ($statistic->activity_id != null) {
                        $act = $activities->where('id', $statistic->activity_id)->first();
                        if ($act && $act->unit) {
                            $arr['sign'] = $act->unit;
                        }
                    }
                    if ($statistic->type == 'formula') {
                        $val = AnalyticStat::calcFormula(
                            statistic: $statistic,
                            date: $date,
                            round: $statistic->decimals,
                            stats: $stats
                        );
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
                        $val = $this->daysSum(
                            $columns,
                            $stats,
                            $row->id
                        );
//                        dd_if(
//                            $row->id == 15374 && $column->id == 26021,
//                            $statistic->toArray(), $val, $columns->whereIn('name', range(1,31))->pluck('id')->toArray()
//                        );
                        $val = round($val, 1);
                        $statistic->show_value = $val;
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }
                    if ($statistic->type == 'avg') {
                        $val = $this->daysAvg($columnIds, $stats, $row->id);
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
                    if ($statistic->type == 'salary_day' && !in_array($column->name, ['sum', 'avg', 'name'])) {
                        $val = $fot[$column->name] ?? 0;
                        $statistic->show_value = $val;
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }
                    if ($statistic->type == 'time') {
                        $day = Carbon::parse($date)->day($column->name)->format('Y-m-d');
                        $group = ProfileGroup::query()->find($dto->groupId);
                        $positions = $group->reportCards->pluck('position_id')->toArray();
                        $divide = $group->reportCards->first()->divide_to ?? 1;
                        $val = Timetracking::totalHours($day, $dto->groupId, $positions);
                        $val = floor($val) / $divide;
                        $val = max($val, 0);

                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = round($val, 1);
                        $arr['show_value'] = round($val, 1);
                    }
                } else {
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

    public function getKeys(Collection $rows, Collection $columns): array
    {
        $rowKeys = $rows->mapWithKeys(function ($row, $index) {
            return [$row->id => $index + 1];
        })->toArray();

        $columnKeys = [];
        $filtered = $columns
            ->filter(fn($item) => $item->name != 'plan')
            ->pluck('id')
            ->toArray();

        foreach ($filtered as $key => $id) {
            $columnKeys[$id] = $key;
        }

        return [
            'rows' => $rowKeys,
            'columns' => $columnKeys
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

    private function totalForDay(Activity $activity, $date): int|float
    {
        $method = ($activity->plan_unit === 'minutes' || $activity->plan_unit === 'less_sum') ? 'sum' : 'avg';

        $total = UserStat::query()
            ->where('activity_id', $activity->id)
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
        array      $columns,
        Collection $stats,
        int        $rowId,
    ): float|int
    {

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

    public function userStatisticFormTable(
        Activity $activity,
        string   $firstOfMoth,
        int      $groupId = null
    ): Collection
    {
        /** @var ProfileGroup $group */
        $group = ProfileGroup::query()->find($groupId);

        $dateFrom = Carbon::createFromDate($firstOfMoth)->endOfMonth()->format('Y-m-d');
        $dateTo = Carbon::createFromDate($firstOfMoth)->addMonth()->startOfMonth()->format('Y-m-d');
        $users = $group->actualAndFiredEmployees($firstOfMoth, $dateTo)
            ->whereDoesntHave('activities')
            ->with('statistics', function (HasMany $query) use ($activity, $firstOfMoth, $dateFrom) {
                $query->selectRaw('DAY(date) as day, user_id, value as value, date')
                    ->where('activity_id', $activity->id)
                    ->where('date', '>=', $firstOfMoth)
                    ->where('date', '<=', $dateFrom);
            })
            ->get()
            ->unique('id')
            ->values();

        return $users->each(function ($employee) use ($firstOfMoth, $activity) {
            $workDay = isset($user->working_day_id) && $user->working_day_id == 1 ? WorkingDay::FIVE_DAYS : WorkingDay::SIX_DAYS;
            $appliedFrom = $employee->workdays_from_applied($firstOfMoth, $workDay);
            $workDays = WorkChartModel::workdaysPerMonth($employee);

            $employee->fullname = $employee->full_name;
            $employee->applied_from = $appliedFrom;
            $employee->plan = $activity->daily_plan * $workDays;
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
        $group = ProfileGroup::query()->whereIn('id', $dto->groupIds)->first();

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

    private function getRentabilityValue($group_id, $date): float|int
    {
        $statRepository = app(AnalyticStatRepository::class);
        $stats = $statRepository->getByGroupId($group_id, $date);
        $val = 0;

        /** @var AnalyticStat $stat */
        $stat = $this->implStat($group_id, $date);
        if ($stat) {
            $val = AnalyticStat::calcFormula(
                statistic: $stat,
                date: $date,
                round: 2,
                stats: $stats
            );
        }

        return $val;
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

        $statRepository = app(AnalyticStatRepository::class);
        $stats = $statRepository->getByGroupId($groupId, $date);

        return $stat ? AnalyticStat::calcFormula(
            statistic: $stat,
            date: $date,
            round: 2,
            only_days: $days,
            stats: $stats
        ) : $impl;
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

    private function implStat(
        int    $groupId,
        string $date
    ): Model|null
    {
        $implStat = null;

        $column = AnalyticColumn::query()
            ->where('group_id', $groupId)
            ->where('date', $date)
            ->where('name', self::VALUE_PLAN)->first() ?? null;

        $row = AnalyticRow::query()
            ->where('group_id', $groupId)
            ->where('date', $date)
            ->where('name', self::VALUE_IMPL)->first() ?? null;

        if ($row && $column) {
            $implStat = AnalyticStat::query()
                ->where('column_id', $column->id)
                ->where('row_id', $row->id)->first();
        }

        return $implStat;
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
            $columns[$column->id] = AnalyticStat::getLetter($index);
        }

        return [
            'rows' => $rows,
            'columns' => $columns
        ];
    }

    private function getGroupPlanColumns($group_id, $date): Builder
    {
        return AnalyticColumn::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'plan');
    }

    private function getGroupImplRows($group_id, $date): Builder|null
    {
        return AnalyticRow::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'Impl');
    }
}
