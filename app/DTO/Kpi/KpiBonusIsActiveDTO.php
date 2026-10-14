<?php
declare(strict_types=1);

namespace App\DTO\Kpi;

final class KpiBonusIsActiveDTO
{
    /**
     * @param int $bonusId
     * @param bool $isActive
     */
    public function __construct(
        public int $bonusId,
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
            'bonus_id' => $this->bonusId,
            'is_active' => $this->isActive
        ];
    }
}