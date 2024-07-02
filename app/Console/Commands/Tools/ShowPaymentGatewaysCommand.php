<?php

namespace App\Console\Commands\Tools;

use App\Facade\Payment\Gateway;
use Illuminate\Console\Command;

class ShowPaymentGatewaysCommand extends Command
{
    protected $signature = 'gateways';

    protected $description = 'Show payment gateways';

    public function handle(): void
    {
        Gateway::fake();
        foreach (Gateway::list() as $gateway => $config) {
            $this->line('/*--------------------------------*/');
            $this->info('Gateway: ' . $gateway);
            foreach ($config as $name => $item) {
                if (is_array($item)) {
                    $this->info("$name: ");
                    foreach ($item as $alias) {
                        $this->info(" - $alias");
                    }
                } else {
                    $this->info("$name: $item");
                }
            }
        }
    }
}
