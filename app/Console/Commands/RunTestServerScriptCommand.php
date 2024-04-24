<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunTestServerScriptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:dev {branch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to fetch all changes from given git branch to this branch and run script to test service';


    public function handle(): void
    {
        // Path to the shell script
        $scriptPath = base_path('test_branch.sh');

        // Execute the script
        $this->info(shell_exec("bash $scriptPath " . $this->argument('branch') . "2>&1"));
    }
}
