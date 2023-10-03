<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

class ReferralDetermination implements ReferralDeterminationInterface
{
    private ReferrerInterface $referrer;

    public function determinate(ReferralInterface $referral): ReferrerInterface
    {
        $this->whoIsReferrer($referral);
        return $this->referrer;
    }

    private function whoIsReferrer(ReferralInterface $referral): void
    {
        $this->referrer = $referral->referrer;
    }
}