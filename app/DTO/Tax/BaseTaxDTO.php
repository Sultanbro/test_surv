<?php

namespace App\DTO\Tax;

abstract class BaseTaxDTO
{
    public function __construct(
        public string $name,
        public float $value,
        public bool $isPercent,
    )
    {}
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'is_percent' => $this->isPercent,
        ];
    } 
}
