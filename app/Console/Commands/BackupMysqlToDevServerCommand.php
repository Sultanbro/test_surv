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
        $script = base_path("db_dump.sh");
        $output = shell_exec("bash $script 2>&1");
        // Log the output or do something with it
        slack($output);
        $this->line($output);
    }
}
