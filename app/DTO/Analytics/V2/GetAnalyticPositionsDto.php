<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;

use Webmozart\Assert\Assert;

class GetAnalyticPositionsDto
{
    /**
     * @param string $groupId
     */
    public function __construct(
        public readonly string $groupId
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

        return new static(
            $data['group_id']
        );
    }
}