<?php

namespace App\DTO\Tax;

class SetUserTaxDTO
{
    /**
     * @param int $taxGroupId
     * @param int $userId
     * @param bool $assigned
     */
    public function __construct(
        public int $taxGroupId,
        public int $userId,
        public bool $assigned
    )
    {}
}
