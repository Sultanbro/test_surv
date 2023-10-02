<?php

namespace Tests;

use App\Models\Tenant;
use Artisan;
use DB;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\TestCase as BaseTestCase;

abstract class TenantTestCase extends BaseTestCase
{
    protected string $originalDatabaseConnection;
    private Tenant $tenant;

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Store the original database connection
        $this->originalDatabaseConnection = config('database.default');

        // Run the central database migrations
        $this->runTestCentralDatabaseMigrations();

        // Create a new tenant for testing
        $this->initializeTestTenant();
    }

    protected function tearDown(): void
    {
        // Delete the test tenant database
        $this->deleteTestTenantDatabase();

        // Reset the central database
        $this->resetCentralDatabase();
        $this->refreshApplication();
        parent::tearDown();
    }

    protected function runTestCentralDatabaseMigrations(): void
    {
        // Switch to the 'testing' database connection
        // Run the migrations for the central database
        Artisan::call('migrate', [
            '--database' => 'testing'
        ]);
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function initializeTestTenant(): void
    {
        // Create and initialize a new tenant for testing
        $tenantId = 'test';
        $this->ensureCanCreateDatabase($tenantId);
        $this->createTenant($tenantId);
        tenancy()->initialize($this->tenant);
    }

    protected function deleteTestTenantDatabase(): void
    {
        tenancy()->end();

        $database = $this->tenant->database()->getName();
        // Delete the tenant database (be cautious with this operation)
        DB::statement("DROP DATABASE IF EXISTS $database");
    }

    protected function resetCentralDatabase(): void
    {
        // Rollback the central database migrations
        Artisan::call('migrate:reset', [
            '--database' => 'testing'
        ]);
    }

    protected function createTenant(string $tenantId): void
    {
        $this->tenant = Tenant::factory()->create([
            'id' => $tenantId
        ]);
    }

    function ensureCanCreateDatabase($tenantId): void
    {
        $database = config('tenancy.database.prefix') . $tenantId;
        // Check if the database exists
        // if database for this tenant exists need to delete it and then re-create
        // this should be done before every test case
        DB::statement("DROP DATABASE IF EXISTS $database");
    }
}

