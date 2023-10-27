<?php

namespace App\Service\Referral;

use App\Service\Referral\Core\GeneratorInterface;
use App\User;

class ReferralService
{
    private GeneratorInterface $generator;

    public function __construct(
        GeneratorInterface $generator,
    )
    {
        $this->generator = $generator;
    }

    public function url(User $user): Core\ReferralUrlDto
    {
        return $this->generator->generate($user);
    }
}