<?php
declare(strict_types=1);

namespace App\DTO\TimeTrack\SalaryWorkChartDTO;


final class SalaryWorkChartDTO
{
    /**
     * @param string $month
     * @param string $year
     * @param string $start_day
     * @param string $hollidays
     */
    public function __construct(
        public string $month,
        public string $year,
        public string $start_day,
        public string $hollidays,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'month' => $this->month,
            'year' => $this->year,
            'start_day' => $this->start_day,
            'hollidays' => $this->hollidays
        ];
    }
}