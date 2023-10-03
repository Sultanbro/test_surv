<?php

namespace App\Service\Referral\Core;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $parent_referrer_id
 * @property-read static $parentReferrer
 * @property-read Collection $referees
 */
interface ReferrerInterface
{
    public function referral(): HasOne;

    public function referees(): HasMany;

    public function parentReferrer(): BelongsTo;

    public function hasParentReferrer(): bool;
}