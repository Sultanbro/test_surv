<?php

namespace Tests\Unit\Service\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\Analytics\Analytics;
use PHPUnit\Framework\TestCase;

class AnalyticTest extends TestCase
{

    public function test_it_can_get_analytics()
    {
        $dto = new  GetAnalyticDto();
        /** @var Analytics $service */
        $service = app(Analytics::class);
        $service->analytics();
    }
}
