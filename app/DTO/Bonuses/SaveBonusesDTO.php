<?php
declare(strict_types=1);

namespace App\DTO\Bonuses;

final class SaveBonusesDTO
{
    /**
     * @param array $bonuses
     */
    public function __construct(
        public array $bonuses
    )
    {
    }
}