<?php

namespace App\DTO\Tax;

class TaxGroupDTO
{
    public function __construct(
        public int|null $id,
        public string $name,
        public array $items
    )
    {
    }
}
