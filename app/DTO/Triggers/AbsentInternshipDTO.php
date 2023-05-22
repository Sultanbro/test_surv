<?php
declare(strict_types=1);

namespace App\DTO\Triggers;

use App\DTO\BaseDTO;

final class AbsentInternshipDTO extends BaseDTO
{
    /**
     * @param int $userId
     */
    public function __construct(
        public int $userId
    )
    {}

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId
        ];
    }
}