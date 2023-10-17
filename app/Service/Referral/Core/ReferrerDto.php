<?php

namespace App\Service\Referral\Core;

use Illuminate\Database\Eloquent\Collection;

class ReferrerDto
{

    /**
     * @param int|null $id
     * @param Collection|null $referees
     * @param int|null $parent_id
     * @param ReferrerInterface|null $parentReferrer
     */
    public function __construct(
        public null|int                 $id
        , public null|Collection        $referees
        , public null|int               $parent_id
        , public null|ReferrerInterface $parentReferrer
    )
    {
    }

    public static function from(ReferrerInterface $referrer): static
    {
        return new static(
            $referrer->id,
            $referrer->referrals,
            $referrer->parent_id,
            $referrer->referrer
        );
    }
}