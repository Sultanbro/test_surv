<?php

namespace App\Console\Commands;

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
        $referral1 = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $referral2 = User::factory()->create([
            'referrer_id' => $referrer->getKey()
        ]);
        $referral2->description()->create([
            'is_trainee' => 0,
            'applied' => now()->format("Y-m-d"),
        ]);
        Lead::factory()->create([
            'user_id' => $referral1->getKey(),
            'lead_id' => $referrer->getKey(),
            'referrer_id' => $referrer->getKey(),
            'name' => "test",
            'phone' => "64545454545454",
            'status' => 'NEW',
            'segment' => 3548,
            'hash' => md5(45454)
        ]);
        Lead::factory()->create([
            'user_id' => $referral2->getKey(),
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
            'user_id' => $referral2->getKey(),
            'lead_id' => $referrer->getKey(),
            'deal_id' => $referrer->getKey(),
            'referrer_id' => $referral1->getKey(),
            'name' => "test",
            'phone' => "64545454545454",
            'status' => 'NEW',
            'segment' => 3548,
            'hash' => md5(45454)
        ]);

        Salary::factory()->create([
            'is_paid' => 1,
            'award' => 10000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referral1->getKey(),
            'date' => now()->format("Y-m-d"),
        ]);

        Salary::factory()->create([
            'is_paid' => 1,
            'award' => 10000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->format("Y-m-d"),
        ]);
        Salary::factory()->create([
            'is_paid' => 0,
            'award' => 5000,
            'resource' => SalaryResourceType::REFERRAL,
            'user_id' => $referrer->getKey(),
            'date' => now()->format("Y-m-d"),
        ]);
        dump($repository->getStatistic([]));
        DB::rollBack();
    }
}
