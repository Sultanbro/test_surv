<?php

namespace App\DTO\Position;

class StorePositionWithDescriptionDTO
{
    /**
     * @param int $id
     * @param string|null $newName
     * @param int|null $indexation
     * @param int|null $sum
     * @param array|null $description
     */
    public function __construct(
        public int $id,
        public ?string $newName,
        public ?int $indexation,
        public ?int $sum,
        public ?array $description
    )
    {

    }
}