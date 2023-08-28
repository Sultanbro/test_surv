<?php

namespace App\DTO\Position;

class DeletePositionDTO
{
    /**
     * @param int $position
     */
    public function __construct(public int $position){}
}