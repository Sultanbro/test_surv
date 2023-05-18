<?php
declare(strict_types=1);

namespace App\DTO\WorkChart;

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

    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            'text_name' => $this->name ?? null,
            'start_time' => $this->startTime ?? null,
            'end_time' => $this->endTime ?? null,
            'work_charts_type' => $this->chartWorkType ?? null,
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