<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\{ReferralDeterminationInterface,
    ReferralGeneratorInterface,
    ReferralInterface,
    ReferrerInterface};
use App\User;

class ReferralService
{
    private ReferralGeneratorInterface $generator;
    private ReferralDeterminationInterface $determination;

    public function __construct(
        ReferralGeneratorInterface     $generator,
        ReferralDeterminationInterface $determination,
    )
    {
        $this->generator = $generator;
        $this->determination = $determination;
    }

    public function generateReferral(User $user): Core\ReferralDto
    {
        return $this->generator->generate($user);
    }

    public function request(ReferralInterface $referral): ReferrerInterface
    {
        return $this->determination->determinate($referral);
    }
}