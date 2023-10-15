<?php

namespace App\Facade\Analytics;

use App\CacheStorage\AnalyticCacheStorage;
use App\DTO\Analytics\V2\GetAnalyticDto;
use App\DTO\Analytics\V2\UtilityDto;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Enums\V2\Analytics\StatEnum;
use App\GroupSalary;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticColumn as Column;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticRow as Row;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\UserStat;
use App\Models\WorkChart\WorkChartModel;
use App\ProfileGroup;
use App\Models\Analytics\TopValue as ValueModel;
use App\QualityRecordWeeklyStat;
use App\Salary;
use App\Service\Department\UserService;
use App\Timetracking;
use App\Traits\AnalyticTrait;
use App\User;
use App\WorkingDay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\Group;

final class Analytics
{
    const VALUE_PLAN = 'plan';
    const VALUE_IMPL = 'Impl';

    use AnalyticTrait;

    public function analytics(GetAnalyticDto $dto): array
    {
        $date       = DateHelper::firstOfMonth($dto->year, $dto->month);

        $rows       = AnalyticRow::query()->where('date', $date)->where('group_id', $dto->groupId)->orderByDesc('order')->get();
        $columns    = AnalyticColumn::query()->where('date', $date)->where('date', $dto->groupId)->get();
        $stats      = AnalyticStat::query()->where('date', $date)->where('group_id', $dto->groupId)->get();
        $activities = Activity::withTrashed()->where('group_id', $dto->groupId)->get();

        $keys = $this->getKeys($rows, $columns);


        $weekdays       = AnalyticStat::getWeekdays($date);
        $tableValues    = $this->analyticTableValue($rows, $columns);
        $table      = [];
        $cellNumber = 0;

        foreach ($rows as $rowIndex => $row)
        {
            $dependingFromRow = $rows->where('depend_id', $row->id)->first();
            $cellNumber       = $rowIndex + 1;
            $addClass         = '';

            foreach ($columns as $columnIndex => $column)
            {
                if (!in_array((int)$column->name, $weekdays) && !in_array($column->name, ['plan', 'sum', 'avg', 'name']))
                {
                    $addClass = ' weekday';
                }

                if (!in_array($column->name, ['sum', 'avg', 'name']))
                {
                    $addClass .= ' text-center';
                }

                if ($dependingFromRow)
                {
                    $addClass .= ' bg-violet';
                }

                $cellLetter = $columnIndex != 0 ? AnalyticStat::getLetter($columnIndex - 1) : 'A';
                $statistic  = $stats->where('row_id', $row->id)->where('column_id', $column->id)->first();

                if ($statistic)
                {
                    $arr = [];

                    if ($statistic->activity_id != null)
                    {
                        $act = $activities->where('id', $statistic->activity_id)->first();
                        if ($act && $act->unit)
                        {
                            $arr['sign'] = $act->unit;
                        }
                    }

                    if ($statistic->type == 'formula')
                    {
                        $val = AnalyticStat::calcFormula($statistic, $date, $statistic->decimals);
                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = AnalyticStat::convert_formula($statistic->value, $keys['rows'], $keys['columns']);
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'stat')
                    {
                        $day = Carbon::parse($date)->day($column->name)->format('Y-m-d');
                        $val = UserStat::total_for_day($statistic->activity_id, $day);
                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'sum')
                    {
                        $val = AnalyticStat::daysSum($date, $row->id, $dto->groupId);
                        $val = round($val, 1);
                        $statistic->show_value = $val;
                        $statistic->save();

                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($statistic->type == 'avg')
                    {
                        $val = AnalyticStat::daysAvg($date, $row->id, $dto->groupId);
                        $statistic->show_value = round($val, 1);
                        $statistic->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }
                }

            }
        }

        return $table;
    }


    /**
     * @param $date
     * @param $row_id
     * @param $group_id
     * @param $days
     * @return float|int
     */
    public function daysSum($date, $row_id, $group_id, $days = []): float|int
    {
        $days = empty($days) ? range(1, 31) : $days;

        $columns = $this->columns($date, $group_id)->where('group_id', $group_id)
            ->where('date', $date)
            ->whereIn('name', $days);

        $total = 0;

        $all_stats = $this->statistics($date, $group_id)->where('row_id', $row_id)
            ->where('date', $date);

        foreach ($columns as $column) {
            $stat = $all_stats->where('column_id', $column->id)->first();

            if ($stat && is_numeric($stat->show_value)) {
                $total += (float)$stat->show_value;
            }
        }

        return $total;
    }
    /**
     * @param Activity $activity
     * @param string $date
     * @param int|null $groupId
     * @return Collection
     */
    public function userStatisticFormTable(
        Activity $activity,
        string $date,
        int $groupId = null
    ): Collection
    {
        $group      = ProfileGroup::query()->where('id', $groupId)->first();
        $dateFrom   = Carbon::createFromDate($date)->endOfMonth()->format('Y-m-d');
        $firstOfMonth = Carbon::createFromDate($date)->firstOfMonth()->format('Y-m-d');
        $dateTo     = Carbon::createFromDate($date)->addMonth()->startOfMonth()->format('Y-m-d');

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
                $workDay        = isset($user->working_day_id) && $user->working_day_id == 1 ? WorkingDay::FIVE_DAYS : WorkingDay::SIX_DAYS;
                $appliedFrom    = $employee->workdays_from_applied($date, $workDay);
                $workDays       = WorkChartModel::workdaysPerMonth($employee);


                $employee->fullname =  $employee->full_name;
                $employee->fired     = $employee->deleted_at != null ? 1 : 0;
                $employee->applied_from  = $appliedFrom;
                $employee->is_trainee    = 1;
                $employee->plan          = $activity->daily_plan * $workDays;


                return $employee;
            });
;
    }

    /**
     * @param GetAnalyticDto $dto
     * @return array
     */
    public function decompositionTable(
        GetAnalyticDto $dto
    ): array
    {
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);
        $decompositions = DecompositionValue::query()->where([
            'group_id'  => $dto->groupId,
            'date'      => $date,
        ])->get();


        return [
            'group_id'  => $dto->groupId,
            'records'   => $decompositions
        ];
    }

    /**
     * @param UtilityDto $dto
     * @return array
     */
    public function utility(
        UtilityDto $dto
    ): array
    {
        $groupIds   = $dto->groupIds ?? ProfileGroup::profileGroupsWithArchived($dto->year, $dto->month, false, false, ProfileGroup::SWITCH_UTILITY);
        $groups     = ProfileGroup::query()->whereIn('id', $groupIds)->get();
        $date       = DateHelper::firstOfMonth($dto->year, $dto->month);
        $gauges     = [];
        foreach ($groups as $group)
        {
            $percent = 0;
            $tops = ValueModel::getByGroupAndDate($group->id, $date)->get()->map(
                function ($top) use ($group, $date, $percent) {
                    return [
                        'id'            => $top->id,
                        'group_id'      => $top->group_id,
                        'place'         => 1,
                        'activity_id'   => $top->activity_id,
                        'unit'          => $top->unit,
                        'editable'      => false,
                        'edit_value'    => false,
                        'diff'          => 0,
                        'cell'          => $top->cell,
                        'round'         => $top->round,
                        'fixed'         => $top->fixed,
                        'is_main'       => $top->is_main,
                        'value_type'    => $top->value_type,
                        'key'           => $top->id * 1000,
                    ] + ValueModel::getDynamicValue($group->id, $date, $top);
                });

            $gauges[] = [
                'group_id'  => $group->id,
                'name'      => $group->name,
                'gauges'    => $tops,
                'group_activities'  => Activity::query()->where('group_id', $group->id)->get(),
                'archive_utility'   => $group->archive_utility,
            ];
        }

        return $gauges;
    }

    public function rentability(
        UtilityDto $dto
    ): array
    {
        $date       = DateHelper::firstOfMonth($dto->year, $dto->month);
        $group      = ProfileGroup::whereIn('id', $dto->groupIds)->first();

        $topValue   = new ValueModel;
        $options    = $topValue->getOptions('[]');
        $options['staticZones'] = $this->getStaticZones($group);

        $options['staticLabels']['labels'] = $this->labels($group);

        return [
            'name'          => $group->name ?? 'Рентабельность',
            'value'         => (float)$this->getRentabilityValue($group->id, $date),
            'group_id'      => $group->id,
            'place'         => 1,
            'unit'          => '%',
            'editable'      => false,
            'edit_value'    => false,
            'activity_id'   => 0,
            'key'           => 999 * 1000,
            'min_value'     => 0,
            'max_value'     => $group->rentability_max,
            'round'         => 2,
            'cell'          => '',
            'is_main'       => 0,
            'fixed'         => 0,
            'value_type'    => 'sum',
            'sections'      => $options['staticLabels']['labels'],
            'options'       => $options,
            'diff'          =>  $this->rentabilityDiff($group->id, $date)
        ];
    }

    /**
     * @param int $groupId
     * @param array $views
     * @return Collection
     */
    public function activitiesViews(
        int $groupId,
        array $views
    ): Collection
    {
        return Activity::query()
            ->where('group_id', $groupId)
            ->whereIn('view', $views)
            ->orderByDesc('order')->get();
    }

    /**
     * @param Collection $rowsData
     * @param Collection $columnsData
     * @return array[]
     */
    private function analyticTableValue(
        Collection $rowsData,
        Collection $columnsData
    ): array
    {
        $rows       = [];
        $columns    = [];

        foreach ($rowsData as $index => $row)
        {
            $rows[$row->id] = $index + 1;
        }

        foreach($columnsData as $index => $column)
        {
            $columns[$column->id] = $index != 0 ? AnalyticStat::getLetter($index - 1) : 'A';
        }

        return [
            'rows'      => $rows,
            'columns'   => $columns
        ];
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return float|int
     */
    private function rentabilityDiff(
        int $groupId,
        string $date
    ): float|int
    {
        $currentMonthImpl   = $this->rentabilityByDay($groupId, $date);
        $prevMonthImpl      = $this->rentabilityByDay($groupId, $date);

        return round($currentMonthImpl - $prevMonthImpl, 2);
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return float|int
     */
    private function rentabilityByDay(
        int $groupId,
        string $date
    ): float|int
    {
        $impl = 0;
        $days = $this->getDaysPerMonth($date);
        $stat = $this->implStat($groupId, $date) ?? null;

        return $stat ? AnalyticStat::calcFormula($stat, $date, 2, $days) : $impl;
    }

    /**
     * @param int $groupId
     * @param string $date
     * @return Model|null
     */
    private function implStat(
        int $groupId,
        string $date
    ): Model|null
    {
        $implStat = null;

        $column  = AnalyticColumn::query()
            ->where('date', $date)
            ->where('name', self::VALUE_PLAN)->first() ?? null;

        $row     = AnalyticRow::query()
            ->where('date', $date)
            ->where('name', self::VALUE_IMPL)->first() ?? null;

        if ($row && $column)
        {
            $implStat = AnalyticStat::query()
                ->where('column_id', $column->id)
                ->where('row_id', $row->id)->first();
        }

        return $implStat;
    }
    
    /**
     * @param string $date
     * @return array
     */
    private function getDaysPerMonth(
        string $date
    ): array
    {
        $date = Carbon::createFromDate($date)->daysInMonth;
        $days = [];

        for ($day = 1; $day <= $date; $day++)
        {
            $days[] = $day;
        }

        return $days;
    }
    /**
     * @param $group_id
     * @param $date
     * @return float|int
     */
    private function getRentabilityValue($group_id, $date): float|int
    {
        $val = 0;

        $column = $this->getGroupPlanColumns($group_id, $date)->first() ?? [];
        $row    = $this->getGroupImplRows($group_id, $date)->first() ?? [];

        if($row && $column) {
            $stat = AnalyticStat::query()->where('column_id', $column->id)
                ->where('row_id', $row->id)
                ->where('date', $date)
                ->first();
            if($stat) {
                $val = AnalyticStat::calcFormula($stat, $date, 2);
                $stat->show_value = $val;
                $stat->save();
            }
        }

        return $val;
    }

    /**
     * @param $group_id
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getGroupPlanColumns($group_id, $date): \Illuminate\Database\Eloquent\Builder
    {
        return AnalyticColumn::query()
            ->where('date', $date)
            ->where('name', 'plan');
    }

    /**
     * @param $group_id
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    private function getGroupImplRows($group_id, $date): \Illuminate\Database\Eloquent\Builder|null
    {
        return AnalyticRow::query()
            ->where('date', $date)
            ->where('name', 'Impl');
    }

    /**
     * @param ProfileGroup|null $group
     * @return array[]
     */
    private function getStaticZones(?ProfileGroup $group): array
    {
        return [
            ['strokeStyle' => "#F03E3E", 'min' => 0, 'max' => 49], // Red
            ['strokeStyle' => "#fd7e14", 'min' => 50, 'max' => 74], // Orange
            ['strokeStyle' => "#FFDD00", 'min' => 75, 'max' => 99], // Yellow
            ['strokeStyle' => "#30B32D", 'min' => 100, 'max' => $group->rentability_max], // Green
        ];
    }

    /**
     * @param ProfileGroup|null $group
     * @return array
     */
    private function labels(?ProfileGroup $group): array
    {
        return [0, 50, 100, $group->rentability_max];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|array $rows
     * @param \Illuminate\Database\Eloquent\Collection|array $columns
     * @return array
     */
    public function getKeys(\Illuminate\Database\Eloquent\Collection|array $rows, \Illuminate\Database\Eloquent\Collection|array $columns): array
    {
        $rowKeys = $rows->mapWithKeys(function ($row, $index) {
            return [$index + 1 => $row->id];
        });

        $columnKeys = $columns->mapWithKeys(function ($column, $index) {
            return [$index + 1 => $column->id];
        });


        return [
            'rows' => $rowKeys,
            'columns' => $columnKeys
        ];
    }
}