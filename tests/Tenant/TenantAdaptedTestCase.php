<?php

namespace Tests\Tenant;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\TestResponse;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\CreatesApplication;
use Tests\TestCase;

abstract class TenantAdaptedTestCase extends TestCase
{
    use CreatesApplication;
    use WithTenancy;
    use DatabaseTransactions;

    protected string $baseUrl = '';

    protected array $permissions = [];

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null): TestResponse
    {
        return parent::call($method, $this->baseUrl . $uri, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        parent::setUp();
        $uses = parent::setUpTraits();
        if (isset($uses[HasAuthUser::class]) && method_exists($this, 'givenUser')) {
            $this->givenUser();
        }
    }
}
