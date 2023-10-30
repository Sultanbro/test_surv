<?php

namespace App\Facade;

use App\Enums\SalaryResourceType;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\Core\ReferralUrlDto;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\StatusServiceInterface;
use App\Service\Referral\Core\TransactionInterface;
use App\Service\Referral\UrlGeneratorService;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ReferralUrlDto url(User $user)
 * @method static void handle(ReferrerInterface $referrer)
 * @use UrlGeneratorService
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
            /** @var StatusServiceInterface $service */
            $service = app(StatusServiceInterface::class);
            $service->touch($user->referrer);
        }
    }

    public static function deleteReferrerDailySalary(int $user_id, string $date): void
    {
        /** @var User $user */
        $user = User::with('description')
            ->find($user_id)
            ->first();
        if (!$user) {
            return;
        }

        if (!$user->referrer) {
            return;
        }

        $salary = $user->referrer->salaries()
            ->where(fn($query) => $query
                ->where('date', $date)
                ->where('comment_award', $user->getKey())
                ->where('award', '<', 5000)
                ->where('resource', SalaryResourceType::REFERRAL)
            )
            ->first();
        $salary?->update([
            'award' => 0
        ]);
    }

    public static function touchReferrerSalaryForCertificate(User $user): void
    {
        /** @var TransactionInterface $service */
        $service = app(TransactionInterface::class);
        /** @var User $user */
        $user = $user->load([
            'description',
            'referrer'
        ]);

        if (!$user->referrer) {
            return;
        }

//        $service->useDate(now()); // this can use when date is not current date
        $service->touch($user->referrer, PaidType::ATTESTATION);
    }

    public static function touchReferrerSalaryForTrain(User $user, Carbon $date): void
    {
        /** @var TransactionInterface $service */
        $service = app(TransactionInterface::class);

        /** @var User $user */
        $user = $user->load([
            'description',
            'referrer'
        ]);

        if (!$user->referrer) {
            return;
        }
        $service->useDate($date); // this can use when date is not current date
        $service->touch($user->referrer, PaidType::TRAINEE);
    }
}