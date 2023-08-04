<?php

namespace App\DTO\Position;

class AnyPositionDTO
{
    /**
     * @param int|string $position
     * @param int|boolean $is_head
     */
    public function __construct(
        public int|string $position,
        public ?bool $is_head
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'position' => $this->position,
            'is_head' => $this->is_head,
        ];
    }
}