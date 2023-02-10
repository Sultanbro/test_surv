<?php

namespace App\DTO\Manager;

class PutManagerToOwnerDTO
{
    /**
     * @param int $ownerId
     * @param int $managerId
     */
    public function __construct(
        public int $ownerId,
        public int $managerId
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'manager_id' => $this->managerId,
            'owner_id'   => $this->ownerId
        ];
    }
}