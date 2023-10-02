<?php

namespace App\Service\Referral;

use App\DTO\BaseDTO;

class ReferralDto extends BaseDTO
{
    /**
     * @param int|null $id
     * @param string|null $url
     * @param int|null $referrer_id
     */
    public function __construct(
        public null|int                 $id
        , public null|string            $url
        , public null|int $referrer_id
    )
    {
    }

    public static function from(ReferralInterface $referrer): static
    {
        return new static(
            $referrer->id,
            $referrer->url(),
            $referrer->referrer_id,
        );
    }

    public function toArray(): array
    {
        return [
              'id' => $this->id
            , 'url' => $this->url
            , 'referrer_id' => $this->referrer_id
        ];
    }
}