<?php

namespace App\DTO\Analytics\V2;

use Webmozart\Assert\Assert;

class AddRowAnalyticsDto
{
    /**
     * @param int $groupId
     * @param array $rows
     * @param int $year
     * @param int $month
     */
    public function __construct(
        public readonly int $groupId,
        public readonly array $rows,
        public readonly int $year,
        public readonly int $month,
    )
    {}

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(
        array $data
    ): self
    {
        Assert::keyExists($data, 'group_id');
        Assert::keyExists($data, 'rows');
        Assert::keyExists($data, 'year');
        Assert::keyExists($data, 'month');

        return new self(
            (int)$data['group_id'],
            $data['rows'],
            (int)$data['year'],
            (int)$data['month']
        );
    }
}