<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantTestCase extends TestCase
{
    use WithTenancy;
    use DatabaseTransactions;
}