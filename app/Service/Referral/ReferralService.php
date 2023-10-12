<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\{ReferralDeterminationInterface,
    ReferralGeneratorInterface,
    ReferralInterface,
    ReferrerInterface,
    ReferrerSalaryHandlerInterface};
use App\User;

class ReferralService
{
    private ReferralGeneratorInterface $generator;
    private ReferralDeterminationInterface $determination;
    private ReferrerSalaryHandlerInterface $salaryHandler;

    public function __construct(
        ReferralGeneratorInterface     $generator,
        ReferralDeterminationInterface $determination,
        ReferrerSalaryHandlerInterface $salaryHandler,
    )
    {
        $this->generator = $generator;
        $this->determination = $determination;
        $this->salaryHandler = $salaryHandler;
    }

    public function generateReferral(User $user): Core\ReferralDto
    {
        return $this->generator->generate($user);
    }

    public function request(ReferralInterface $referral): ReferrerInterface
    {
        return $this->determination->determinate($referral);
    }

    public function handle(ReferrerInterface $referral): void
    {
        $this->salaryHandler->handle($referral);
    }
}