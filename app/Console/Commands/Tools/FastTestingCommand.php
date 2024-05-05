<?php

namespace App\Console\Commands\Tools;

use App\Facade\Payment\Gateway;
use App\Service\Payments\WalletOne\WalletOneGateway;
use Illuminate\Console\Command;

class FastTestingCommand extends Command
{
    protected $signature = 'test:service';

    protected $description = 'just for fast testing... :)';

    public function handle(): void
    {
        $gateway = Gateway::get("kzt");
    }
}
