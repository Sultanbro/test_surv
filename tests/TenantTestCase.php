<?php

namespace Tests;

use Database\Seeders\AdaptedTenantDatabaseForTesting;
use Drfraker\SnipeMigrations\SnipeMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Throwable;

abstract class TenantTestCase extends BaseTestCase
{
    use CreatesApplication, SnipeMigrations;

    protected string $defaultConnection = 'testing';

    /**
     * @throws Throwable
     */

    public function initializeTenancy($class = AdaptedTenantDatabaseForTesting::class): void
    {
        $this->seed($class);
        $this->defaultConnection = config('database.default');
        config([
            'database.default' => 'tenant'
        ]);
    }

    /**
     * @throws Throwable
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initializeTenancy();
    }

    /**
     * @throws Throwable
     */
    protected function tearDown(): void
    {
        tenancy()->end();
        config([
            'database.default' => $this->defaultConnection
        ]);
        parent::tearDown();
    }
}

