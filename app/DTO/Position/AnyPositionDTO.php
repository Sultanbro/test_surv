<?php

namespace App\DTO\Position;

class AnyPositionDTO
{
    /**
     * @param int $position
     */
    public function __construct(
        public int $position
    )
    {}
}