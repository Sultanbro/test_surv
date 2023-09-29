<?php
declare(strict_types=1);

namespace App\Service\Referral;

use App\User;
use Str;

class ReferralGenerator implements ReferralGeneratorInterface
{
    public function generate(User $user): string
    {
        /** @var ReferrerInterface $referrer */
        $referrer = $user->asReferrer()->firstOrCreate();

        /** @var ReferralInterface $referral */
        $referral = $referrer->referral()->firstOrCreate([
            'referer_id' => $referrer->id
        ], [
            'token' => Str::uuid()->toString()
        ]);

        return $referral->url();
    }
}