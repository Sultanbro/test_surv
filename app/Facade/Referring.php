<?php

namespace App\Facade;

use App\DayType;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\ReferralUrlDto;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\StatusServiceInterface;
use App\Service\Referral\Core\TransactionInterface;
use App\Service\Referral\UrlGeneratorService;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ReferralUrlDto url(User $user)
 * @method static void handle(ReferrerInterface $referrer)
 * @use UrlGeneratorService
 */
class Referring extends Facade
{
    protected static $cached = true;

    public static function touchReferrerStatus(User $user): void
    {
        $referrer = $user->referrer;
        if (!$referrer) return; // if a user doesn't have a referrer, then just return;
        /** @var StatusServiceInterface $service */
        $service = app(StatusServiceInterface::class);
        $service->touch($user->referrer);
    }

    public static function touchReferrerSalaryForCertificate(User $user, ?Carbon $date = null): void
    {
        /** @var TransactionInterface $service */
        $service = app(TransactionInterface::class);
        /** @var User $user */
        $user = $user->load([
            'description',
            'referrer'
        ]);

        if (!$user->referrer) return; // if a user doesn't have a referrer, then just return;
        $service->useDate($date ?? now());
        $service->touch($user, PaidType::ATTESTATION);
    }

    public static function touchReferrerSalaryDaily(User $user, Carbon $date): void
    {
        /** @var TransactionInterface $service */
        $service = app(TransactionInterface::class);
        $userCurrentGroupStartingDate = $user->activeGroup()->pivot->from;

        /** @var User $exists */
        $exists = User::withTrashed()
            ->where('id', $user->id)
            ->whereHas('daytypes', function (Builder $query) use ($date, $userCurrentGroupStartingDate) {
                $query->whereIn("type", [DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']]);
                $query->where('date', $date->format("Y-m-d"));
                $query->where('date', '>=', $userCurrentGroupStartingDate);
            })
            ->with([
                'description',
                'referrer',
            ])
            ->first();

        if (!$exists) {
            self::deleteReferrerDailySalary($user->id, $date);
            return;
        }

        if (!$exists->referrer) {
            return;
        } // if a user doesn't have a referrer, then just return;

        $service->useDate($date); // this can be used when the date is not current
        $service->touch($exists, PaidType::TRAINEE);
    }

    public static function deleteReferrerDailySalary(int $user_id, Carbon $date): void
    {
        /** @var User $referral */
        $referral = User::with(['description', 'referrer'])
            ->find($user_id);

        /** @var User $referrer */
        $referrer = User::query()->find($referral->referrer_id);

        if (!$referrer) return; // if a user doesn't have a referrer, then just return;

        $referrer->referralSalaries()
            ->where('date', $date->format("Y-m-d"))
            ->where('referral_id', $referral->getKey())
            ->where('type', PaidType::TRAINEE->name)
            ->first()
            ?->delete();
    }

    public static function touchReferrerSalaryWeekly(User|Authenticatable $user, Carbon $date): void
    {
        /** @var TransactionInterface $service */
        $service = app(TransactionInterface::class);
        $service->useDate($date);

        $userCurrentGroupStartingDate = $user->activeGroup()->pivot->from;

        /** @var User $user */
        $user = User::withTrashed()
            ->where('id', $user->id)
            ->with([
                'description',
                'referrer'
            ])
            ->withCount(['timetracking' => fn(Builder $query) => $query->whereRaw("TIMESTAMPDIFF(minute, `enter`, `exit`) >= 180")
                ->where("enter", '>=', $userCurrentGroupStartingDate)
            ])
            ->withCount(['referralSalaries' => fn(Builder $query) => $query->where("type", '>=', $userCurrentGroupStartingDate)
                ->where("type", PaidType::WORK->name)
            ])
            ->first();

        if (!$user->referrer) return; // if a user doesn't have a referrer, then just return;

        $workedWeeksCount = (int)floor($user->timetracking_count / 6);

        if ($workedWeeksCount < 1) return;

        if ($workedWeeksCount == 1) {
            $service->touch($user, PaidType::FIRST_WORK);
            return;
        }

        if ($workedWeeksCount == 5) $workedWeeksCount = 4;
        else if ($workedWeeksCount == 7) $workedWeeksCount = 6;
        else if (in_array($workedWeeksCount, [9, 10, 11])) $workedWeeksCount = 8;

        if (!in_array($workedWeeksCount, [2, 3, 4, 6, 8, 12])) return;


        dd_if($user->id === 30604, [
            'count' => $user->referralSalaries_count
        ]);

        $service->touch($user, PaidType::FIRST_WORK);
        $service->touch($user, PaidType::WORK);
    }

    protected static function getFacadeAccessor(): string
    {
        return 'referral';
    }
}