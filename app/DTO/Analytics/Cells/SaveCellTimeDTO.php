<?php
declare(strict_types=1);

namespace App\DTO\Analytics\Cells;

final class SaveCellTimeDTO
{
    /**
     * @param int $groupId
     * @param int $rowId
     * @param string $class
     * @param int $year
     * @param int $month
     */
    public function __construct(
        public int $groupId,
        public int $rowId,
        public string $class,
        public int $year,
        public int $month
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'group_id'  => $this->groupId,
            'row_id'    => $this->rowId,
            'class'     => $this->class,
            'year'      => $this->year,
            'month'     => $this->month
        ];
    }
}