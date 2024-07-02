<?php

namespace App\DTO\Permissions;

class SwitchAccessDTO
{
    /**
     * @param array $accesses
     * @param int $userId
     * @param bool $IsAccess
     */
    public function __construct(
        public array $accesses,
        public int $userId
    )
    {
    }

    /**
     * @return array[]
     */
    public function toArray(): array
    {
        return [
            'accesses'  => $this->accesses,
            'user_id'   => $this->userId
        ];
    }
}