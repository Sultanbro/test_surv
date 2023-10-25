<?php

namespace App\Facade;

use App\DayType;
use App\Enums\SalaryResourceType;
use App\Jobs\Referral\ProcessTouchReferrerStatus;
use App\Service\Referral\Core\ReferralUrlDto;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\ReferralService;
use App\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ReferralUrlDto url(User $user)
 * @method static void handle(ReferrerInterface $referrer)
 * @use ReferralService
 */
class Referring extends Facade
{
    protected static $cached = true;


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'referral';
    }

    public static function touchReferrerStatus(User $user): void
    {
        if ($user->referrer_id) {
            ProcessTouchReferrerStatus::dispatch($user->referrer)
                ->afterCommit();
        }
    }

    public static function touchReferrerSalary($user_id, $type, $date): void
    {
        /** @var User $user */
        $user = User::with('description')
            ->find($user_id)
            ->first();
        $referrer = $user?->referrer;
        if (!$referrer) {
            return;
        }

        if (in_array($type, [DayType::DAY_TYPES['ABCENSE'], DayType::DAY_TYPES['FIRED']])) {
            $salary = $referrer->salaries()
                ->where('date', $date)
                ->where('award', '=', 1000)
                ->where('is_paid')
                ->where('resource', SalaryResourceType::REFERRAL)
                ->first();
            $salary?->delete();
        }
    }

}