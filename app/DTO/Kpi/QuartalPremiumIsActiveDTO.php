<?php
declare(strict_types=1);

namespace App\DTO\Kpi;

final class QuartalPremiumIsActiveDTO
{
    /**
     * @param int $premiumId
     * @param bool $isActive
     */
    public function __construct(
        public int $premiumId,
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
            'premium_id' => $this->premiumId,
            'is_active' => $this->isActive
        ];
    }
}