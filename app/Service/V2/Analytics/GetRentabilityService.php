<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetRentabilityDto;
use App\GroupSalary;
use App\Helpers\DateHelper;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\TopEditedValue;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use App\Salary;
use Carbon\Carbon;
use Exception;

/**
* Класс для работы с Service.
*/
class GetRentabilityService
{
    /**
     * @param GetRentabilityDto $dto
     * @return array
     * @throws Exception
     */
    public function handle(GetRentabilityDto $dto): array
    {
        $gauges = ProfileGroup::withRentability($dto->year, $dto->month)
            ->map(fn ($group) => TopValue::query()->where('group_id', $group->id)->where('type', TopValue::RENTABILITY)->get())
            ->filter(fn ($group) => $group->count() > 0)->toArray();

        return [
            'table'         => TopValue::getPivotRentability($dto->year, $dto->month),
            'speedometers'  => $gauges
        ];
    }
}