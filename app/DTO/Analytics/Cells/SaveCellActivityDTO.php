<?php
declare(strict_types=1);

namespace App\DTO\Analytics\Cells;

final class SaveCellActivityDTO
{
    /**
     * @param int $groupId
     * @param int $rowId
     * @param int $activityId
     * @param string $class
     * @param int $year
     * @param int $month
     */
    public function __construct(
        public int $groupId,
        public int $rowId,
        public int $activityId,
        public string $class,
        public int $year,
        public int $month
    )
    {
    }
}