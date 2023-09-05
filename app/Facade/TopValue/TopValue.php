<?php

namespace App\Facade\TopValue;

use App\CacheStorage\AnalyticCacheStorage;
use App\DTO\Analytics\V2\UtilityDto;
use App\Enums\V2\Analytics\AnalyticEnum;
use App\Helpers\DateHelper;
use App\Models\Analytics\AnalyticColumn as Column;
use App\Models\Analytics\AnalyticRow as Row;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use App\Models\Analytics\TopValue as ValueModel;
use App\Traits\AnalyticTrait;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class TopValue
{
    use AnalyticTrait;

    /**
     * @param UtilityDto $dto
     * @return array
     */
    public function utility(
        UtilityDto $dto
    ): array
    {
        $groupIds   = $dto->groupIds ?? ProfileGroup::profileGroupsWithArchived($dto->year, $dto->month, false, false, ProfileGroup::SWITCH_UTILITY);
        $groups     = $this->groups()->whereIn('id', $groupIds);
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
                'group_activities'  => collect(AnalyticCacheStorage::get(AnalyticEnum::ANALYTIC_ACTIVITIES))->where('group_id', $group->id),
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
        $group      = $this->groups()->whereIn('id', $dto->groupIds)->first();
        $topValue   = new ValueModel;
        $options    = $topValue->getOptions('[]');

        $options['staticZones'] = [
            [ 'strokeStyle' => "#F03E3E", 'min' => 0, 'max' => 49 ], // Red
            [ 'strokeStyle' => "#fd7e14", 'min' => 50, 'max' => 74 ], // Orange
            [ 'strokeStyle' => "#FFDD00", 'min' => 75, 'max' => 99 ], // Yellow
            [ 'strokeStyle' => "#30B32D", 'min' => 100, 'max' => $group->rentability_max ], // Green
        ];

        $options['staticLabels']['labels'] = [0,50,100, $group->rentability_max];

        return [
            'name'          => 'Рентабельность',
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
            'diff'          =>  AnalyticStat::getRentabilityDiff($group->id, $date)
        ];
    }

    /**
     * @param $group_id
     * @param $date
     * @return float|int
     */
    private function getRentabilityValue($group_id, $date): float|int
    {
        $val = 0;

        $column = $this->getGroupPlanColumns($group_id, $date) ?? [];
        $row    = $this->getGroupImplRows($group_id, $date) ?? [];

        if($row && $column) {
            $stat = $this->statistics()->where('column_id', $column->id)
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
     * @return Collection|null
     */
    private function getGroupPlanColumns($group_id, $date): Collection|null
    {
        return $this->columns()->where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'plan')
            ->first();
    }

    /**
     * @param $group_id
     * @param $date
     * @return Collection|null
     */
    private function getGroupImplRows($group_id, $date): Collection|null
    {
        return $this->rows()->where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'Impl')
            ->first();
    }
}