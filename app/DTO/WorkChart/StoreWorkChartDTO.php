<?php
declare(strict_types=1);

namespace App\DTO\WorkChart;

final class StoreWorkChartDTO
{
    /**
     * @param string $name
     * @param string $startTime
     * @param string $endTime
     */
    public function __construct(
        public string $name,
        public int $chartWorkdays,
        public int $chartDayoffs,
        public string $startTime,
        public string $endTime,
        public int $chartWorkType,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->chartWorkdays .'-'. $this->chartDayoffs,
            'text_name' => $this->name,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'work_charts_type' => $this->chartWorkType,
        ];
    }
}