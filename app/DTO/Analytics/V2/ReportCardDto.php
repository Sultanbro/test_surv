<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;

use Webmozart\Assert\Assert;

class ReportCardDto
{
    /**
     * @param int $groupId
     * @param int $rowId
     * @param int $year
     * @param int $month
     * @param float $divide
     * @param array $positions
     */
    public function __construct(
        public readonly int $groupId,
        public readonly int $rowId,
        public readonly int $year,
        public readonly int $month,
        public readonly float $divide,
        public readonly array $positions,
    )
    {}

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(
        array $data
    ): static
    {
        Assert::keyExists($data, 'group_id');
        Assert::keyExists($data, 'row_id');
        Assert::keyExists($data, 'year');
        Assert::keyExists($data, 'month');
        Assert::keyExists($data, 'divide');
        Assert::keyExists($data, 'positions');

        return new static(
            $data['group_id'],
            $data['row_id'],
            $data['year'],
            $data['month'],
            $data['divide'],
            $data['positions'] ?? null
        );
    }
}