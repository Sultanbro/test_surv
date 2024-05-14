<?php

namespace Tests\Feature\Payment;

use Tests\Tenant\HasAuthUser;
use Tests\Tenant\TenantAdaptedTestCase as TestCase;

class GetTariffsEndpointTest extends TestCase
{
    use HasAuthUser;

    public function test_user_can_get_tariffs_endpoint(): void
    {
        $domain = tenant('domain');
        $this->json('get/');
    }
}