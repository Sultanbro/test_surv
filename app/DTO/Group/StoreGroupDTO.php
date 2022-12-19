<?php
declare(strict_types=1);

namespace App\DTO\Group;

final class StoreGroupDTO
{
    /**
     * @param string $name
     */
    public function __construct(
        public string $name
    )
    {}
}