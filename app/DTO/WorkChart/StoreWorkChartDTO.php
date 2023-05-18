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
        public string $startTime,
        public string $endTime,
        public int $chartWorkType,
        public int $chartWorkdays,
        public int $chartDayoffs,
        public int $usualSchedule,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'text_name' => $this->name,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'work_charts_type' => $this->chartWorkType,
        ];

        if ($this->chartWorkType === 1) {
            $data['workdays'] = $this->usualSchedule;
            $data['name'] = $this->chartWorkdays .'-'. $this->chartDayoffs;
        }
        else{
            $data['name'] = $this->chartWorkdays .'-'. $this->chartDayoffs;
        }
        return $data;
    }
}