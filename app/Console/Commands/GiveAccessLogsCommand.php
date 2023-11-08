<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GiveAccessLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give access to every day logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        shell_exec('chmod -R 777 storage/tenantbp/logs');
        shell_exec('chmod -R 777 storage/logs');

        return "success";
    }
}
