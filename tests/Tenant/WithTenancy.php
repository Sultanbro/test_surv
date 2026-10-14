<?php

namespace Tests\Tenant;

use App\Models\Tenant;
use App\Providers\TenancyServiceProvider;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
        $tenantDto = new DefaultTestTenantDto;

        Tenant::query()
            ->where('id', $tenantDto->id)
            ->existsOr(function () use ($tenantDto) {
                DB::unprepared("DROP DATABASE IF EXISTS " . $tenantDto->db);
                Tenant::query()->create($tenantDto->toArray());
            });


        tenancy()->initialize($tenantDto->id);

        if (isset($uses[DatabaseTransactions::class]) || isset($uses[RefreshDatabase::class])) {
            $this->beginTenantDatabaseTransaction();
        }

        if (isset($uses[DatabaseMigrations::class]) || isset($uses[RefreshDatabase::class])) {
            $this->beforeApplicationDestroyed(function () {
                tenancy()->end();
            });
        }
    }

    public function beginTenantDatabaseTransaction(): void
    {
        $database = $this->app->make('db');

        $connection = $database->connection(TenancyServiceProvider::$dbConnection);
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);

        $this->beforeApplicationDestroyed(function () use ($database) {
            $connection = $database->connection(TenancyServiceProvider::$dbConnection);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->rollBack();
            $connection->setEventDispatcher($dispatcher);
            $connection->disconnect();
        });
    }
}
