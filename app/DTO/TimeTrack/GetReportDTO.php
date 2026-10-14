<?php

namespace App\DTO\TimeTrack;

class GetReportDTO
{
    public function __construct(
        public int $groupId,
        public ?int $year,
        public ?int $month,
        public ?int $day
    )
    {
    }
}