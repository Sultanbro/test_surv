<?php

namespace App\DTO\Tax;

class EditUserTaxDTO
{
    /**
     * @param int $year
     * @param int $month
     * @param int $userId
     * @param string $reason
     * @param int|null $taxGroupId
     */
    public function __construct(
        public int $year,
        public int $month,
        public int $userId,
        public string $reason,
        public int|null $taxGroupId,
    )
    {}
}
