<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\{GeneratorInterface, ReferrerInterface, SalaryHandlerInterface};
use App\User;

class ReferralService
{
    private GeneratorInterface $generator;
    private SalaryHandlerInterface $salaryHandler;

    public function __construct(
        GeneratorInterface     $generator,
        SalaryHandlerInterface $salaryHandler,
    )
    {
        $this->generator = $generator;
        $this->salaryHandler = $salaryHandler;
    }

    public function url(User $user): Core\ReferralUrlDto
    {
        return $this->generator->generate($user);
    }

    public function handle(ReferrerInterface $referrer): void
    {
        $this->salaryHandler->handle($referrer);
    }
}