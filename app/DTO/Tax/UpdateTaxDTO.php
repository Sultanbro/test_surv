<?php

namespace App\DTO\Tax;

class UpdateTaxDTO
{
    public function __construct(
        public int $id,
        public ?string $name,
        public ?float $value,
        public ?bool $isPercent,
    )
    {}
}
