<?php

namespace App\DTO\Position;

class AnyPositionDTO
{
    /**
     * @param int|string $position
     * @param int|boolean $is_head
     * @param int|boolean $is_spec
     */
    public function __construct(
        public int|string $position,
        public ?bool $is_head,
        public ?bool $is_spec,
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
            'is_spec' => $this->is_spec,
        ];
    }
}