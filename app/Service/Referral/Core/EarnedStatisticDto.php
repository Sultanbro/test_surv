<?php

namespace App\Service\Referral\Core;

use App\DTO\BaseDTO;

class EarnedStatisticDto extends BaseDTO
{
    /**
     * @param int|null $mine
     * @param int|null $referrers
     * @param int|null $whole
     */
    public function __construct(
          public null|int   $mine
        , public null|int $referrers
        , public null|int $whole
    )
    {
    }

    public function toArray(): array
    {
        return [
              'mine' => $this->mine
            , 'referrers' => $this->referrers
            , 'whole' => $this->whole
        ];
    }
}