<?php

namespace App\Service\Referral;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $referrer_id
 * @property string $token
 * @property-read ReferrerInterface $referrer
 */
interface ReferralInterface
{
    public function referrer(): BelongsTo;

    public function url(): string;
}