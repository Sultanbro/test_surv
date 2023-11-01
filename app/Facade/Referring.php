<?php

namespace App\Facade;

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

    public static function deleteReferrerDailySalary(int $user_id, Carbon $date): void
    {
        /** @var User $referral */
        $referral = User::with(['description', 'referrer'])
            ->find($user_id)
            ->first();

        $referrer = $referral?->referrer;

        if (!$referrer) {
            return;
        }

        $salary = $referrer->referralSalaries()
            ->where(fn($query) => $query
                ->where('date', $date->format("Y-m-d"))
                ->where('referral_id', $referral->getKey())
                ->where('type', PaidType::TRAINEE)
            )
            ->first();
        $salary?->update([
            'amount' => 0
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
        $service->touch($user, PaidType::ATTESTATION);
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

        $service->useDate($date); // this can be used, when date is not now
        $service->touch($user, PaidType::TRAINEE);
    }
}