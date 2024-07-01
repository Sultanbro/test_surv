<?php

namespace App\Service\Referral\Core;

use App\DTO\BaseDTO;

class StatisticDto extends BaseDTO
{
    /**
     * @param array $data
     */
    public function __construct(
        public array $data = []
    )
    {
    }

    public function toArray(): array
    {
        return $this->data;
    }
}