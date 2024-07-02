<?php
declare(strict_types=1);

namespace App\DTO\WorkChart\User;

final class DeleteUserChartDTO
{
    /**
     * @param int $userId
     */
    public function __construct(
        public int $userId
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
        ];
    }
}