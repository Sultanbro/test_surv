<?php
declare(strict_types=1);

namespace App\Service\Referral;

use App\User;
use Illuminate\Support\Str;

class ReferralGenerator implements ReferralGeneratorInterface
{
    public function generate(User $user): ReferralDto
    {
        /** @var ReferrerInterface $referrer */
        $referrer = $user->asReferrer()->firstOrCreate();

        /** @var ReferralInterface $referral */
        $referral = $referrer->referral()->firstOrCreate([
            'referrer_id' => $referrer->id
        ], [
            'token' => Str::uuid()->toString()
        ]);

        return ReferralDto::from($referral);
    }
}