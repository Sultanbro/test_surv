<?php
declare(strict_types=1);

namespace App\DTO\Group;

final class RestoreGroupDTO
{
    /**
     * @param int $id
     */
    public function __construct(
        public int $id
    )
    {}
}