<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\DTO\Analytics\V2\UtilityDto;
use App\Facade\TopValue\TopValueFacade;
use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
* Класс для работы с Service.
*/
final class GetPerformanceService
{
    public function handle(GetAnalyticDto $dto): array
    {
        $utilityDto = UtilityDto::fromArray([
            'group_ids' => array($dto->groupId),
            'year'      => $dto->year,
            'month'     => $dto->month
        ]);

        $utility = TopValueFacade::utility($utilityDto);
    }
}