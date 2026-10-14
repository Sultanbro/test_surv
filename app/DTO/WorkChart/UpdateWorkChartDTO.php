<?php
declare(strict_types=1);

namespace App\DTO\WorkChart;

use App\Models\WorkChart\WorkChartModel;

final class UpdateWorkChartDTO
{
    /**
     * @param ?string $name
     * @param ?string $startTime
     * @param ?string $endTime
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $startTime,
        public string $endTime,
        public int $chartWorkType,
        public int $chartWorkdays,
        public int $chartDayoffs,
        public int $usualSchedule,
        public int $restTime,
        public int $floatingDayoffs,

    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'text_name' => $this->name,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'work_charts_type' => $this->chartWorkType,
            'name' => $this->chartWorkdays .'-'. $this->chartDayoffs,
            'workdays' => $this->chartWorkType == WorkChartModel::WORK_CHART_TYPE_USUAL ? $this->usualSchedule : null,
            'rest_time' => $this->restTime,
            'floating_dayoffs' => $this->floatingDayoffs,
        ];
    }
}