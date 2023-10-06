<?php

namespace Tests;

use App\Models\Tenant;
use Drfraker\SnipeMigrations\SnipeMigrations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class TenantTestCase extends BaseTestCase
{
    use CreatesApplication, SnipeMigrations;

    private Tenant|Model $tenant;

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->initializeTenancy();
    }

    protected function tearDown(): void
    {
        tenancy()->end();
        $this->tenant->delete();
        parent::tearDown();
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Throwable
     */
    public function initializeTenancy(): void
    {
        DB::commit();
        $this->tenant = Tenant::query()->create();
        DB::beginTransaction();
        tenancy()->initialize($this->tenant);
    }
}

