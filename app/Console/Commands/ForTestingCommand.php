<?php

namespace App\Console\Commands;

use App\Models\Bitrix\Lead;
use App\Repositories\Referral\StatisticRepository;
use App\User;
use Illuminate\Console\Command;

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
     * @throws \Throwable
     */
    public function handle(StatisticRepository $repository): void
    {
        config([
            'database.default' => 'tenant'
        ]);
//        DB::beginTransaction();
        $user = User::factory()->create();
        Lead::query()->create([
            'user_id' => $user->getKey(),
            'lead_id' => $user->getKey(),
            'name' => "test",
            'phone' => "64545454545454",
            'status' => 'NEW',
            'segment' => 3548,
            'hash' => md5(45454)
        ]);
        dump($repository->getStatistic($user));
//        DB::rollBack();
    }
}
