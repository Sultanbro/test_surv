<?php

namespace App\DTO\Tax;

class TaxGroupDTO
{
    public function __construct(
        public string $name,
        public array $items
    )
    {
    }
}
