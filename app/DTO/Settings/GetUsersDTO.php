<?php

namespace App\DTO\Settings;

class GetUsersDTO
{
    public function __construct(
        public string $type,
        public int $positionId,
        public int $segment,
        public ?string $startDate,
        public ?string $endDate,
        public ?string $startDateDeactivate,
        public ?string $endDateDeactivate,
        public ?string $startDateApplied,
        public ?string $endDateApplied
    )
    {}

    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            'positionId' => $this->positionId,
            'segment'   => $this->segment,
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate,
            'startDateDeactivate' => $this->startDateDeactivate,
            'endDateDeactivate' => $this->endDateDeactivate,
            'startDateApplied'  => $this->startDateApplied,
            'endDateApplied'    => $this->endDateApplied
        ];
    }
}