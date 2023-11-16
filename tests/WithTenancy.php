<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

trait WithTenancy
{
    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function setUpTraits(): array
    {
        $uses = parent::setUpTraits();

        if (isset($uses[WithTenancy::class])) {
            $this->initializeTenancy($uses);
        }

        return $uses;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function initializeTenancy(array $uses): void
    {
        $tenantId = 'test';

        $tenant = Tenant::query()->firstOr(function () use ($tenantId) {
            $dbName = config('tenancy.database.prefix') . $tenantId;

            DB::unprepared("DROP DATABASE IF EXISTS `$dbName`");
            /** @var Tenant $tenant */
            $tenant = Tenant::query()->create(['id' => $tenantId]);

            if (!$tenant->domains()->count()) {
                $tenant->domains()->create(['domain' => $tenantId]);
            }

            return $tenant;
        });

        tenancy()->initialize($tenant);
        URL::forceRootUrl('http://' . $tenantId . '.localhost');


        if (isset($uses[DatabaseTransactions::class]) || isset($uses[RefreshDatabase::class])) {
            $this->beginTenantDatabaseTransaction();
        }

        if (isset($uses[DatabaseMigrations::class]) || isset($uses[RefreshDatabase::class])) {
            $this->beforeApplicationDestroyed(function () use ($tenant) {
                $tenant->delete();
            });
        }
    }

    public function beginTenantDatabaseTransaction(): void
    {
        $database = $this->app->make('db');

        $connection = $database->connection('tenant');
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);

        $this->beforeApplicationDestroyed(function () use ($database) {
            $connection = $database->connection('tenant');
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->rollBack();
            $connection->setEventDispatcher($dispatcher);
            $connection->disconnect();
        });
    }
}