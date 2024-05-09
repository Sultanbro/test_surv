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
        Gateway::register(['new test', 'new test 2','new test 3'], fn() => 'another test');
        dd(Gateway::list());
    }
}
