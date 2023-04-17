<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RestartQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restart-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Artisan::call('queue:restart');
        exec("sudo supervisorctl stop queue-worker:*");
        exec("sudo supervisorctl reread");
        exec("sudo supervisorctl update");
        exec("sudo supervisorctl start queue-worker:*");
    }
}
