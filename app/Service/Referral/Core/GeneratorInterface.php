<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

use App\User;

interface GeneratorInterface
{
    public function generate(ReferrerInterface $referrer): ReferralUrlDto;
}