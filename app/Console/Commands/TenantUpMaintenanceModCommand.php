<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantUpMaintenanceModCommand extends Command
{
    protected $signature = 'tenants:up';

    protected $description = 'The command to up from the maintenance mode for each tenant';

    public function handle(): void
    {
        tenancy()->runForMultiple(Tenant::all()->toArray(), function (Tenant $tenant) {
            $tenant->update(['maintenance_mode' => null]);
        });
    }
}
