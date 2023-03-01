<?php
declare(strict_types=1);

namespace App\DTO\WorkChart\User;

final class AddUserChartDTO
{
    /**
     * @param int $userId
     * @param int $workChartId
     */
    public function __construct(
        public int $userId,
        public int $workChartId,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'work_chart_id' => $this->workChartId,
        ];
    }
}