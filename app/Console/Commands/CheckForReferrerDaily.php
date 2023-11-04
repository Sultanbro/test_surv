<?php

namespace App\Console\Commands;

use App\DayType;
use App\Service\Referral\Core\CalculateInterface;
use App\Service\Referral\Core\PaidType;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CheckForReferrerDaily extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrer:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        /** @var CalculateInterface $calculateService */
        $calculateService = app(CalculateInterface::class);
        $date = now()->format("Y-m-d");

        /** @var Collection<User> $trainers */
        $trainers = User::query()
            ->whereNotNull('referrer_id')
            ->whereRelation('description', 'is_trainee', 1)
            ->get();

        foreach ($trainers as $trainer) {
            $count = $trainer->daytypes()
                ->where('date', $date)
                ->where('type', DayType::DAY_TYPES['TRAINEE'])
                ->count();
            if ($count) {
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
                $amount = $calculateService->calculate($referrer, PaidType::FIRST_WORK);
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'referral_id' => $employee->id,
                        'comment' => $employee->name,
                        'amount' => $amount,
                        'is_paid' => 0,
                        'type' => PaidType::FIRST_WORK->name,
                    ]);
            }

            $amount = $calculateService->calculate($referrer, PaidType::WORK);
            if ($daysCount === 12) {
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'amount' => $amount,
                        'referral_id' => $employee->id,
                        'is_paid' => 0,

                        'type' => PaidType::WORK->name,
                    ]);
            }
            if ($daysCount === 18) {
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'amount' => $amount,
                        'referral_id' => $employee->id,
                        'is_paid' => 0,
                        'type' => PaidType::WORK->name,
                    ]);
            }
            if ($daysCount === 24) {
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'amount' => $amount,
                        'referral_id' => $employee->id,
                        'is_paid' => 0,
                        'type' => PaidType::WORK->name,
                    ]);
            }
            if ($daysCount === 30) {
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'amount' => $amount,
                        'referral_id' => $employee->id,
                        'is_paid' => 0,
                        'type' => PaidType::WORK->name,
                    ]);
            }
            if ($daysCount === 36) {
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'amount' => $amount,
                        'referral_id' => $employee->id,
                        'is_paid' => 0,
                        'type' => PaidType::WORK->name,
                    ]);
            }
            if ($daysCount === 42) {
                $referrer
                    ->referralSalaries()
                    ->updateOrCreate([
                        'date' => $date,
                        'amount' => $amount,
                        'referral_id' => $employee->id,
                        'is_paid' => 0,
                        'type' => PaidType::WORK->name,
                    ]);
            }
        }
        return CommandAlias::SUCCESS;
    }
}
