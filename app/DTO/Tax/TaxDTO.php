<?php

namespace App\DTO\Tax;

class TaxDTO extends BaseTaxDTO
{
    public function __construct(
        public int $id,
        string $name,
        float $value,
        bool $isPercent,
        public bool $isAssigned,
        bool $end_subtraction,
    )
    {
        parent::__construct(
            $name,
            $value,
            $isPercent,
            $end_subtraction
        );
    }
}
