<?php

namespace App\Console\Commands;

use App\DayType;
use App\Enums\SalaryResourceType;
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
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $trainer->id,
                        'note' => $trainer->name,
                        'award' => 1000,
                        'is_paid' => 0,
                    ]);
            }
        }

        /** @var Collection<User> $employees */
        $employees = User::query()
            ->whereNotNull('referrer_id')
            ->whereRelation('description', 'is_trainee', 0)
            ->whereRelation('description', 'applied', '>=', $date)
            ->get();

        foreach ($employees as $employee) {
            $referrer = $employee->referrer;
            $daysCount = $employee->timetracking()
                ->where('total_hours', '>', 3 * 60)
                ->count();
            // TODO: refactor this
            if ($daysCount === 6) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 10000,
                        'is_paid' => 0,
                    ]);
            }
            if ($daysCount === 12) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 5000,
                        'is_paid' => 0,
                    ]);
            }
            if ($daysCount === 18) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 5000,
                        'is_paid' => 0,
                    ]);
            }
            if ($daysCount === 24) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 5000,
                        'is_paid' => 0,
                    ]);
            }
            if ($daysCount === 30) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 5000,
                        'is_paid' => 0,
                    ]);
            }
            if ($daysCount === 36) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 5000,
                        'is_paid' => 0,
                    ]);
            }
            if ($daysCount === 42) {
                $referrer
                    ->salaries()
                    ->create([
                        'date' => $date,
                        'amount' => 0,
                        'resource' => SalaryResourceType::REFERRAL,
                        'comment_award' => $employee->id,
                        'award' => 5000,
                        'is_paid' => 0,
                    ]);
            }
        }
        return CommandAlias::SUCCESS;
    }
}
