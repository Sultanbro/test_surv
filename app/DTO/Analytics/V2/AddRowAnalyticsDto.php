<?php

namespace App\DTO\Analytics\V2;

use Webmozart\Assert\Assert;

class AddRowAnalyticsDto
{
    /**
     * @param int $groupId
     * @param int $afterRowId
     * @param string $date
     */
    public function __construct(
        public readonly int $groupId,
        public readonly int $afterRowId,
        public readonly string $date,
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
        Assert::keyExists($data, 'after_row_id');
        Assert::keyExists($data, 'date');

        return new self(
            (int)$data['group_id'],
            (int)$data['after_row_id'],
            (string)$data['date'],
        );
    }
}