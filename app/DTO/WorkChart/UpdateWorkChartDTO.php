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
        public ?string $name,
        public ?int $chartWorkdays,
        public ?int $chartDayoffs,
        public ?string $startTime,
        public ?string $endTime,
        public ?int $chartWorkType,

    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'name' => null,
            'text_name' => $this->name ?? null,
            'start_time' => $this->startTime ?? null,
            'end_time' => $this->endTime ?? null,
            'work_charts_type' => $this->chartWorkType ?? null,
        ];

        if (isset($this->chartWorkdays, $this->chartDayoffs)) {
            $array['name'] = $this->chartWorkdays .'-'. $this->chartDayoffs;
        }

        return $array;
    }
}