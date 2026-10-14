<?php

namespace App\DTO\TimeTrack;

class StoreManuallyReportDTO
{
    /**
     * @param int $userId
     * @param int|null $year
     * @param int|null $month
     * @param int|null $day
     * @param string $time
     * @param string|null $comment
     */
    public function __construct(
        public int $userId,
        public int $year,
        public int $month,
        public int $day,
        public string $time,
        public ?string $comment
    )
    {}
}