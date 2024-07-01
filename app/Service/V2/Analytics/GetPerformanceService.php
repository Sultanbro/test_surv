<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\DTO\Analytics\V2\UtilityDto;
use App\Facade\Analytics\AnalyticsFacade;
use App\Helpers\DateHelper;
use App\Models\Analytics\TopValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

/**
* Класс для работы с Service.
*/
final class GetPerformanceService
{
    /**
     * Получаем полезности.
     *
     * @param GetAnalyticDto $dto
     * @return array
     */
    public function handle(GetAnalyticDto $dto): array
    {
        $utilityDto = UtilityDto::fromArray([
            'group_ids' => array($dto->groupId),
            'year'      => $dto->year,
            'month'     => $dto->month
        ]);
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);

//        $utility     = AnalyticsFacade::utility($utilityDto);
        $staticRentability = AnalyticsFacade::rentability($utilityDto);

        return [
            'utility'       => TopValue::utility($dto->groupId)->where('date', $date)->get(),
            'rentability'   => TopValue::rentability($dto->groupId)->where('date', $date)->get(),
            'static_rentability' => $staticRentability
        ];
    }
}