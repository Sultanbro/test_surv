<?php
declare(strict_types=1);

namespace App\DTO\Analytics\Statistics;

final class UpdateUserStatDTO
{
    /**
     * @param int $activityId
     * @param int $groupId
     * @param int $employeeId
     * @param string $value
     * @param int $year
     * @param string $month
     * @param int $day
     */
    public function __construct(
        public int $activityId,
        public int $groupId,
        public int $employeeId,
        public string $value,
        public int $year,
        public string $month,
        public int $day
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'activity_id'   => $this->activityId,
            'group_id'      => $this->groupId,
            'employee_id'   => $this->employeeId,
            'value'         => $this->value,
            'year'          => $this->value,
            'month'         => $this->month,
            'day'           => $this->day
        ];
    }
}