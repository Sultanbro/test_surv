<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantMaintenanceModCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:down';

    protected $description = 'The command to down the maintenance mode an each tenant';

    public function handle(): void
    {
        tenancy()->runForMultiple(Tenant::all()->toArray(), function (Tenant $tenant) {
            $tenant->putDownForMaintenance();
        });
    }
}
