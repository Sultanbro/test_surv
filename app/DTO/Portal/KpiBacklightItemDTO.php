<?php
declare(strict_types=1);

namespace App\DTO\Portal;

final class KpiBacklightItemDTO
{
    /**
     * @param float $startValue [0, 100]
     * @param string $color hex
     */
    public function __construct(
        public float $startValue,
        public string $color,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'startValue' => $this->startValue,
            'color' => $this->color
        ];
    }
}