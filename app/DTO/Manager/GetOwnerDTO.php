<?php

namespace App\DTO\Manager;

class GetOwnerDTO
{
    /**
     * @param int $managerId
     */
    public function __construct(
        public int $managerId
    )
    {
    }
}