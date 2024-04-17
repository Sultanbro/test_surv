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
     * @param bool $is_head
     * @param bool $is_spec
     * @param bool|null $ckp_status
     * @param string|null $ckp
     * @param string|null $ckp_link
     */
    public function __construct(
        public int $id,
        public ?string $newName,
        public ?int $indexation,
        public ?int $sum,
        public ?array $description,
        public ?bool $is_head,
        public ?bool $is_spec,
        public ?bool $ckp_status,
        public ?string $ckp,
        public ?string $ckp_link,
    )
    {

    }
}
