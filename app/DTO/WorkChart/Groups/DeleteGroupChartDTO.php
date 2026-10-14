<?php
declare(strict_types=1);

namespace App\DTO\WorkChart\Groups;

final class DeleteGroupChartDTO
{
    /**
     * @param int $groupId
     */
    public function __construct(
        public int $groupId
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->groupId,
        ];
    }
}