<?php

namespace App\DTO\Top;

use App\DTO\BaseDTO;

class SwitchTopDTO extends BaseDTO
{
    /**
     * @param int $groupId
     * @param bool $isArchive
     */
    public function __construct(
        public int $id,
        public string $switchColumn,
        public int $switchValue
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'switchColumn' => $this->switchColumn,
            'switchValue' => $this->switchValue
        ];
    }
}