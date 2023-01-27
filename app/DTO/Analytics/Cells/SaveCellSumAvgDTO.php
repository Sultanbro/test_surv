<?php
declare(strict_types=1);

namespace App\DTO\Analytics\Cells;

final class SaveCellSumAvgDTO
{
    /**
     * @param int $groupId
     * @param int $rowId
     * @param int $columnId
     * @param string $class
     * @param int $year
     * @param int $month
     */
    public function __construct(
        public int $groupId,
        public int $rowId,
        public int $columnId,
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
            'column_id' => $this->columnId,
            'class'     => $this->class,
            'year'      => $this->year,
            'month'     => $this->month
        ];
    }
}