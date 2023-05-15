<?php

namespace App\DTO\Tax;

class UpdateTaxDTO
{
    /**
     * @param int $id
     * @param string|null $name
     * @param float|null $value
     * @param bool|null $isPercent
     * @param int $userId
     */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?float $value,
        public ?bool $isPercent,
        public int $userId
    )
    {}

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'name'          => $this->name,
            'value'         => $this->value,
            'is_percent'    => $this->isPercent,
            'user_id'       => $this->userId
        ];
    }
}
