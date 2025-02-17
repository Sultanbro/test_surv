<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;
use Webmozart\Assert\Assert;

final class CreateAnalyticDto
{
    /**
     * @param int $groupId
     * @param int $year
     * @param int $month
     */
    public function __construct(
        public readonly int $groupId,
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
        Assert::keyExists($data, 'year');
        Assert::keyExists($data, 'month');

        return new self(
            (int)$data['group_id'],
            (int)$data['year'],
            (int)$data['month']
        );
    }
}