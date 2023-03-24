<?php
declare(strict_types=1);

namespace App\DTO\Portal;

final class KpiBacklightDTO
{
    /**
     * @param array<KpiBacklightItemDTO> $kpiBacklightItems,
     */
    public function __construct(
        public array $kpiBacklightItems,
    )
    {}
}