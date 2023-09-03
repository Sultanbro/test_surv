<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\TopValue\TopValueFacade;
use Illuminate\Support\Facades\Schema;

/**
* Класс для работы с Service.
*/
final class GetPerformanceService
{
    public function handle(GetAnalyticDto $dto): array
    {
        $utility = TopValueFacade::utility();
    }
}