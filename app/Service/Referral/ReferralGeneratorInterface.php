<?php
declare(strict_types=1);

namespace App\Service\Referral;

use App\User;

interface ReferralGeneratorInterface
{
    public function generate(User $user): ReferralDto;
}