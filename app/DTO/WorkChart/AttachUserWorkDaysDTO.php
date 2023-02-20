<?php
declare(strict_types=1);

namespace App\DTO\WorkChart;

final class AttachUserWorkDaysDTO
{
    /**
     * @param int $userId
     * @param array $workdays
     */
    public function __construct(
        public int $userId,
        public array $workdays
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'workdays' => $this->workdays
        ];
    }
}