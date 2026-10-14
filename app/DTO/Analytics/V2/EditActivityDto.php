<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;

class EditActivityDto
{
    /**
     * @param array $activity
     * @param ?array $employees
     * @param int $year
     * @param int $month
     */
    public function __construct(
        public readonly array $activity,
        public readonly ?array $employees,
        public readonly int $year,
        public readonly int $month
    )
    {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(
        array $data
    ): self
    {
        return new self(
            $data['activity'],
            $data['employees'] ?? null,
            $data['year'],
            $data['month']
        );
    }
}