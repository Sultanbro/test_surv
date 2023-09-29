<?php
declare(strict_types=1);

namespace App\Service\Referral;

class ReferralDetermination implements ReferralDeterminationInterface
{
    private ReferrerInterface $referrer;

    public function __construct(
        private readonly ReferralCalculatorInterface $calculator
    )
    {
    }

    public function determinate(ReferralInterface $referral): void
    {
        $this->whoIsReferrer($referral);
        $this->calculator->calculate($this->referrer);
    }

    private function whoIsReferrer(ReferralInterface $referral): void
    {
        $this->referrer = $referral->referrer;
    }
}