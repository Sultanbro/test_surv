<?php
declare(strict_types=1);

namespace App\Service\Top;

use App\DTO\Top\ArchiveUtilityDTO;
use App\ProfileGroup;

/**
* Класс для работы с Service.
*/
class ArchiveUtilityForGroupService
{
    /**
     * @param ArchiveUtilityDTO $dto
     * @return bool
     */
    public function handle(
        ArchiveUtilityDTO $dto
    ): bool
    {
        $group = ProfileGroup::getById($dto->groupId);

        return $group->archive_utility != $dto->isArchive ? $group?->update([
            'archive_utility' => $dto->isArchive
        ]) : false;
    }
}