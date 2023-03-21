<?php

namespace App\DTO\Tax;

class GetTaxesDTO
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
            'user_id' => $this->userId
        ];
    }
}