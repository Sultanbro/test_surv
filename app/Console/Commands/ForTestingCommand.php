<?php

namespace App\Console\Commands;

use App\DayType;
use App\Enums\SalaryResourceType;
use App\Models\Bitrix\Lead;
use App\Repositories\Referral\StatisticRepository;
use App\Salary;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ForTestingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * @throws Throwable
     */
    public function handle(StatisticRepository $repository): void
    {
        config([
            'database.default' => 'tenant'
        ]);
        DB::beginTransaction();
        $referrer = User::factory()->create();
        $referrer->description()->create([
            'is_trainee' => 0,
            'applied' => now()->subDays(5)->format("Y-m-d"),
        ]);

        $employee = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $applet_at = now()->format("Y-m-d");
        $employee->description()->create([
            'is_trainee' => 0,
            'applied' => $applet_at,
        ]);

        $trainee = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $trainee->description()->create([
            'is_trainee' => 1,
            'applied' => 0,
        ]);

        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);
        $employee->timetracking()->create([
            'enter' => now()->subHours(5)
                ->format("Y-m-d"),
            'exit' => now()
                ->format("Y-m-d")
            ,
            'total_hours' => 5,
        ]);

        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->subDays()->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['TRAINEE'],
            'email' => 'test@gmail.com',
        ]);
        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->subDays(2)->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['TRAINEE'],
            'email' => 'test@gmail.com',
        ]);
        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->subDays(3)->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['TRAINEE'],
            'email' => 'test@gmail.com',
        ]);
        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->subDays(4)->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['RETURNED'],
            'email' => 'test@gmail.com',
        ]);
        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->subDays()->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['TRAINEE'],
            'email' => 'test@gmail.com',
        ]);
        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->subDays(5)->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['ABCENSE'],
            'email' => 'test@gmail.com',
        ]);
        $trainee->daytypes()->create([
            'admin_id' => $referrer->getKey(),
            'date' => now()->format('Y-m-d'),
            'type' => DayType::DAY_TYPES['TRAINEE'],
            'email' => 'test@gmail.com',
        ]);

        Lead::factory()->create([
            'user_id' => $trainee->getKey(),
            'lead_id' => $referrer->getKey(),
            'deal_id' => $referrer->getKey(),
            'referrer_id' => $referrer->getKey(),
            'name' => "test",
            'phone' => "64545454545454",
            'status' => 'NEW',
            'segment' => 3548,
            'hash' => md5(45454)
        ]);
        Lead::factory()->create([
            'user_id' => $trainee->getKey(),
            'lead_id' => $referrer->getKey(),
            'deal_id' => $referrer->getKey(),
            'referrer_id' => $referrer->getKey(),
            'name' => "test",
            'phone' => "64545454545454",
            'status' => 'NEW',
            'segment' => 3548,
            'hash' => md5(45454)
        ]);

        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 10000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => $applet_at,
        ]);

        Salary::factory()->create([
            'is_paid' => 0,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays()->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 0,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(1)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(2)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(3)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(4)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(5)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $trainee->getKey(),
            'award' => 1000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->format("Y-m-d"),
        ]);

        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays()->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(2)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(3)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(4)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->subDays(5)->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 1,
            'comment_award' => $employee->getKey(),
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->format("Y-m-d"),
        ]);

        dump($repository->statistic([]));
        DB::rollBack();
    }
}
