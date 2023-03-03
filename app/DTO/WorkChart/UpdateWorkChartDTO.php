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
        public ?string $name,
        public ?string $startTime,
        public ?string $endTime,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name ?? null,
            'start_time' => $this->startTime ?? null,
            'end_time' => $this->endTime ?? null
        ];
    }
}