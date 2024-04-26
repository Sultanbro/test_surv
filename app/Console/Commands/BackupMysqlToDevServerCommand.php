<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupMysqlToDevServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to backup mysql to dev server';

    public function handle(): void
    {
        $script = app_path("db_dump.sh");
        shell_exec($script);
    }
}
