<?php

namespace Tests\Unit;

use Tests\Tenant\DefaultTestTenantDto;
use Tests\Tenant\TenantAdaptedTestCase as TestCase;

class CreateTenantForTestingTest extends TestCase
{

    public function test_can_create_tenant_for_feature_testing(): void
    {
        $tenantDto = new DefaultTestTenantDto;
        $tenant = tenant();

        $this->assertEquals($tenantDto->id, $tenant->id);
        $this->assertEquals($tenant->name, $tenantDto->name);
        $this->assertEquals($tenant->domain, $tenantDto->domain);
        $this->assertEquals($tenant->tenancy_db_name, $tenantDto->db);
    }
}
