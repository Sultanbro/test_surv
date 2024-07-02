<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;
final class UtilityDto
{
    /**
     * @param array $groupIds
     * @param string|int $year
     * @param string|int $month
     */
    public function __construct(
        public readonly array $groupIds,
        public readonly string|int $year,
        public readonly string|int $month
    )
    {}

    public static function fromArray(
        array $data
    ): self
    {
        return new self(
            $data['group_ids'] ?? [],
            $data['year'],
            $data['month']
        );
    }
}