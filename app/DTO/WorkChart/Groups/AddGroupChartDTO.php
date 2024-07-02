<?php
declare(strict_types=1);

namespace App\DTO\WorkChart\Groups;

final class AddGroupChartDTO
{
    /**
     * @param int $groupId
     * @param int $workChartId
     */
    public function __construct(
        public int $groupId,
        public int $workChartId,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group_id' => $this->groupId,
            'work_chart_id' => $this->workChartId,
        ];
    }
}