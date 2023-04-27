<?php

namespace App\DTO\Position;

class AnyPositionDTO
{
    /**
     * @param int|string $position
     */
    public function __construct(
        public int|string $position
    )
    {}
}