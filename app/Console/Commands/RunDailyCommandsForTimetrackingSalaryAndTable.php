<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $this->call('tenants:run salary:update');
        $this->call('tenants:run timetracking:check');
        $this->call('tenants:run count:hours');
    }
}
