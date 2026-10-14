<?php
declare(strict_types=1);

namespace App\DTO\Top;

use App\DTO\BaseDTO;

class ArchiveUtilityDTO extends BaseDTO
{
    /**
     * @param int $groupId
     * @param bool $isArchive
     */
    public function __construct(
        public int $groupId,
        public bool $isArchive
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group_id'   => $this->groupId,
            'is_archive' => $this->isArchive
        ];
    }
}