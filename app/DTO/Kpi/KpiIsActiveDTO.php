<?php
declare(strict_types=1);

namespace App\DTO\Kpi;

final class KpiIsActiveDTO
{
    /**
     * @param int $kpiId
     * @param bool $isActive
     */
    public function __construct(
        public int $kpiId,
        public bool $isActive
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'kpi_id' => $this->kpiId,
            'is_active' => $this->isActive
        ];
    }
}