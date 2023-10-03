<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

interface ReferralDeterminationInterface
{
    public function determinate(ReferralInterface $referral): ReferrerInterface;
}