<?php

namespace App\Service\Referral\Core;

use App\DTO\BaseDTO;

class ReferralUrlDto extends BaseDTO
{
    /**
     * @param string|null $url
     * @param int|null $referrer_id
     */
    public function __construct(
        public null|string $url
        , public null|int  $referrer_id
    )
    {
    }

    public static function make(string $url, int|null $referrerId): static
    {
        return new static($url, $referrerId);
    }

    public function toArray(): array
    {
        return [
              'url' => $this->url
            , 'referrer_id' => $this->referrer_id
        ];
    }
}