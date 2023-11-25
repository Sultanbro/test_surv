<?php

namespace App\Service\Referral;

use App\DayType;
use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\User;
use Illuminate\Database\Eloquent\Collection;

class ForReferrerDaily
{
    public function __construct(
        private readonly CalculateInterface $calculate
    )
    {
    }

    public function handle(): void
    {
        $date = now()->format("Y-m-d");

        /** @var Collection<User> $trainers */
        $trainers = User::query()
            ->whereNotNull('referrer_id')
            ->whereRelation('description', 'is_trainee', 1)
            ->get();

        foreach ($trainers as $trainer) {
            $exists = $trainer->daytypes()
                ->where('date', $date)
                ->where('type', DayType::DAY_TYPES['TRAINEE'])
                ->exists();
            if ($exists) {
                $trainer->referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'referral_id' => $trainer->id,
                        'comment' => $trainer->name,
                        'amount' => 1000,
                        'is_paid' => 0,
                        'type' => PaidType::TRAINEE->name,
                    ]);
            }
        }

        /** @var Collection<User> $employees */
        $employees = User::query()
            ->whereNotNull('referrer_id')
            ->whereRelation('description', 'is_trainee', 0)
            ->get();

        foreach ($employees as $employee) {

            $referrer = $employee->referrer;

            $daysCount = $employee->timetracking()
                ->where('total_hours', '>', 3 * 60)
                ->count();

            if ($daysCount === 6) {
                $this->touchSalaries($referrer, $employee, $date, PaidType::FIRST_WORK);
            } elseif (in_array($daysCount, [12, 18, 24, 30, 36, 42])) {
                $this->touchSalaries($referrer, $employee, $date, PaidType::WORK);
            }

        }
    }

    private function touchSalaries(
        User     $referrer,
        User     $referral,
        string   $date,
        PaidType $type,
        int      $level = 1
    ): void
    {
        $amount = $this->calculate->calculate($referrer, $type, $level);
        $referrer
            ->referralSalaries()
            ->updateOrCreate([
                'date' => $date,
                'referral_id' => $referral->id,
                'comment' => $referral->name,
                'amount' => $amount,
                'is_paid' => 0,
                'type' => $type->name,
            ]);

        if ($type !== PaidType::FIRST_WORK) return;

        $parent = $referrer->referrer;

        if ($parent && $level < 4) {
            $this->touchSalaries($parent, $referral, $date, $type, $level + 1);
        }
    }
}