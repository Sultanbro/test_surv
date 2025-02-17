<?php

namespace App\Service\Referral\Core;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $referrer_id
 * @property string $referrer_status
 * @property-read static $referrer
 * @property Collection<User> $referrals
 */
interface ReferrerInterface
{
    public function referrals(): HasMany;
    public function appliedReferrals(): HasMany;

    public function referrer(): BelongsTo;
    public function hasParent(): bool;

    public function url(): string;
}