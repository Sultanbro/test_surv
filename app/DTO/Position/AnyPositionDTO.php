<?php

namespace App\DTO\Position;

class AnyPositionDTO
{
    /**
     * @param string $position
     */
    public function __construct(
        public string $position
    )
    {}
}