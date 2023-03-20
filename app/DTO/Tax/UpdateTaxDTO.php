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

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'is_percent' => $this->isPercent
        ];
    }
}
