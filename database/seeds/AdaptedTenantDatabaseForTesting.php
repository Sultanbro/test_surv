<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class AdaptedTenantDatabaseForTesting extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function run(): void
    {
        if (!database_exists('tenanttest')) {
            Tenant::query()->create([
                'id' => 'test'
            ]);
        }
        tenancy()->initialize('test');
    }
}
