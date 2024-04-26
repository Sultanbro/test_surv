<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunDailyCommandsForTimetrackingSalaryAndTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:required-commands {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to create timetracking salary and table records';

    public function handle(): void
    {
        $date = $this->argument('date') ?? now()->format('Y-m-d');

        Artisan::call('tenants:run salary:update', ['date' => $date]);
        Artisan::call('tenants:run timetracking:check', ['date' => $date]);
        Artisan::call('tenants:run count:hours', ['date' => $date]);
    }
}
