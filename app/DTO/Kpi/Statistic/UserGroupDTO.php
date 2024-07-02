<?php
declare(strict_types=1);

namespace App\DTO\Kpi\Statistic;

use App\DTO\BaseDTO;

final class UserGroupDTO extends BaseDTO
{
    /**
     * @param int $userId
     * @param int|null $year
     * @param int|null $month
     */
    public function __construct(
        public int $userId,
        public ?int $year,
        public ?int $month
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id'   => $this->userId,
            'year'      => $this->year,
            'month'     => $this->month
        ];
    }
}