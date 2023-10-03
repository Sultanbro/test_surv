<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\ReferralDeterminationInterface;
use App\Service\Referral\Core\ReferralGeneratorInterface;
use App\Service\Referral\Core\ReferralInterface;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\ReferrerSalaryCalculatorInterface;
use App\User;

class ReferralService
{
    private ReferralGeneratorInterface $generator;
    private ReferralDeterminationInterface $determination;
    private ReferrerSalaryCalculatorInterface $calculator;

    public function __construct(
        ReferralGeneratorInterface        $generator,
        ReferralDeterminationInterface    $determination,
        ReferrerSalaryCalculatorInterface $calculator
    )
    {
        $this->generator = $generator;
        $this->determination = $determination;
        $this->calculator = $calculator;
    }

    public function generateReferral(User $user): Core\ReferralDto
    {
        return $this->generator->generate($user);
    }

    public function determinateReferral(ReferralInterface $referral): ReferrerInterface
    {
        return $this->determination->determinate($referral);
    }

    public function calculate(ReferrerInterface $referrer): array
    {
        return $this->calculator->calculate($referrer);
    }
}