<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;

use Webmozart\Assert\Assert;

final class GetRentabilityDto
{
    /**
     * @param string $year
     * @param string $month
     */
    public function __construct(
        public readonly string $year,
        public readonly string $month,
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
        Assert::keyExists($data, 'year');
        Assert::keyExists($data, 'month');

        return new self(
            (string)$data['year'],
            (string)$data['month']
        );
    }
}