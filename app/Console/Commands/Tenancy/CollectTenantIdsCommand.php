<?php

namespace App\Console\Commands\Tenancy;

use App\Models\Page;
use App\Models\Tenant;
use Illuminate\Console\Command;

class CollectTenantIdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:tenant:ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tenants = Tenant::query()->get();
        $ids = [];
        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            $settingsPage = Page::query()->where('key', 'settings')->first();
            if (!$settingsPage) {
                $ids[] = $tenant->id;
            }
        }
        dump($ids);
    }
}
