<?php
declare(strict_types=1);

namespace App\Service\Referral\Core;

class Generator implements GeneratorInterface
{
    public function generate(ReferrerInterface $referrer): ReferralUrlDto
    {
        return ReferralUrlDto::make(
              $referrer->url()
            , $referrer->id
        );
    }
}