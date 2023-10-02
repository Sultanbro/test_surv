<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\Traits\CanConfigureMigrationCommands;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\TestCase as BaseTestCase;

abstract class TenantTestCase extends BaseTestCase
{
    use CanConfigureMigrationCommands;

    protected string $tenantId = 'test';
    protected string $connectionsToTransact = 'testing';

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->cleanupTestCentralDatabase();
        $this->initializeTenancy();
    }

    protected function tearDown(): void
    {
        tenancy()->end();
        parent::tearDown();
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function initializeTenancy(): void
    {
        $tenant = Tenant::query()->create([
            'id' => $this->tenantId
        ]);

        tenancy()->initialize($tenant);
    }

    private function cleanupTestCentralDatabase(): void
    {
        $this->refreshDatabase();
    }

    protected function migrateFreshUsing(): array
    {
        $seeder = $this->seeder();

        return array_merge(
            [
                '--drop-views' => $this->shouldDropViews(),
                '--drop-types' => $this->shouldDropTypes()
            ],
            $seeder ? ['--seeder' => $seeder] : ['--seed' => $this->shouldSeed()]
        );
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function refreshDatabase(): void
    {
        $this->beforeRefreshingDatabase();

        $this->usingInMemoryDatabase()
            ? $this->refreshInMemoryDatabase()
            : $this->refreshTestDatabase();

        $this->afterRefreshingDatabase();
    }

    /**
     * Determine if an in-memory database is being used.
     *
     * @return bool
     */
    protected function usingInMemoryDatabase(): bool
    {
        $default = config('database.default');

        return config("database.connections.$default.database") === ':memory:';
    }

    /**
     * Refresh the in-memory database.
     *
     * @return void
     */
    protected function refreshInMemoryDatabase(): void
    {
        $this->artisan('migrate', $this->migrateUsing());

        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * The parameters that should be used when running "migrate".
     *
     * @return array
     */
    protected function migrateUsing(): array
    {
        return [
            '--seed' => $this->shouldSeed(),
            '--seeder' => $this->seeder(),
            '--database' => $this->connectionsToTransact,
        ];
    }

    /**
     * Refresh a conventional test database.
     *
     * @return void
     */
    protected function refreshTestDatabase(): void
    {
        if (!RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', $this->migrateFreshUsing());

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $this->beginDatabaseTransaction();
    }

    /**
     * Begin a database transaction on the testing database.
     *
     * @return void
     */
    public function beginDatabaseTransaction(): void
    {
        $database = $this->app->make('db');
        $connection = $database->connection($this->connectionsToTransact);
        $dispatcher = $connection->getEventDispatcher();
        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);

        if ($this->app->resolved('db.transactions')) {
            $this->app->make('db.transactions')->callbacksShouldIgnore(
                $this->app->make('db.transactions')->getTransactions()->first()
            );
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            $connection = $database->connection($this->connectionsToTransact);
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->rollBack();
            $connection->setEventDispatcher($dispatcher);
            $connection->disconnect();
        });
    }

    /**
     * The database connections that should have transactions.
     *
     * @return string
     */
    protected function connectionsToTransact(): string
    {
        return $this->connectionsToTransact;
    }

    /**
     * Perform any work that should take place before the database has started refreshing.
     *
     * @return void
     */
    protected function beforeRefreshingDatabase(): void
    {
        $database = config('tenancy.database.prefix') . $this->tenantId;
        // Check if the database exists
        // If the database for this tenant exists, drop it and then re-create it
        DB::statement("DROP DATABASE IF EXISTS $database");
    }

    /**
     * Perform any work that should take place once the database has finished refreshing.
     *
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
    }
}


