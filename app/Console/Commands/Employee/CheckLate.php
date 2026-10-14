<?php

namespace App\Console\Commands\Employee;

use App\Service\Timetrack\UserLateService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CheckLate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:late {date?} {user?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Штрафы за опоздание';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $service = new UserLateService(Carbon::parse($this->argument('date') ?? Carbon::now()));
        $users = User::query()
            ->when($this->argument('user'), function ($query, $argument) {
                return $query->where('id', $argument);
            })
            ->withWhereHas('user_description', fn($query) => $query->where('is_trainee', 0))
            ->orderBy('last_name')
            ->get();
        foreach ($users as $user) {
            $service->userLateFee($user);
        }
    }
}