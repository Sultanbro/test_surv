<?php

namespace App\Console\Commands\Tools;

use App\Facade\Payment\Gateway;
use Illuminate\Console\Command;

class FastTestingCommand extends Command
{
    protected $signature = 'test:service';

    protected $description = 'just for fast testing... :)';

    public function handle(): void
    {
        dd(Gateway::list());
    }
}
