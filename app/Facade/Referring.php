<?php

namespace App\Facade;

use App\Service\Referral\Core\ReferralDto;
use App\Service\Referral\Core\ReferralInterface;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\ReferralService;
use App\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ReferralDto generateReferral(User $user)
 * @method static ReferrerInterface determinateReferral(ReferralInterface $referral)
 * @method static array calculate(ReferrerInterface $referrer)
 * @use ReferralService
 */
class Referring extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'referral';
    }

}