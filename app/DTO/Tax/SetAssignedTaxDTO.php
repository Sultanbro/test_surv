<?php

namespace App\DTO\Tax;

class SetAssignedTaxDTO
{
    /**
     * @param int $taxId
     * @param int $userId
     * @param bool $isAssigned
     */
    public function __construct(
        public int $taxId,
        public int $userId,
        public bool $isAssigned = true,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tax_id' => $this->taxId,
            'user_id' => $this->userId,
            'is_assigned' => $this->isAssigned,
        ];
    }
}