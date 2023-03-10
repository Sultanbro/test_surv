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
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'chartWorkdays' => $this->chartWorkdays,
            'chartDayoffs' => $this->chartDayoffs,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];
    }
}