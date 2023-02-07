<?php

namespace App\DTO\Manager;

class GetOwnerInfoDTO
{
    /**
     * @param int $ownerId
     */
    public function __construct(
        public int $ownerId
    )
    {
    }
}