<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetRentabilityDto;
use App\Helpers\DateHelper;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use Exception;
use Illuminate\Database\Eloquent\Builder;

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
        $gauges = TopValue::query()->whereHas('groups', function (Builder $group) use ($dto){
            $group->whereIn('has_analytics', [ProfileGroup::HAS_ANALYTICS, ProfileGroup::ARCHIVED])
                ->whereNotIn('id', [ProfileGroup::BUSINESS_CENTER_ID, ProfileGroup::IT_DEPARTMENT_ID])
                ->where('active', ProfileGroup::IS_ACTIVE)
                ->where(fn($q) => $q->whereNull('archived_date')->orWhere(fn($query) => $query->whereYear('archived_date', '>=', $dto->year)
                    ->whereMonth('archived_date', '>=', $dto->month)
                ));
        })->where('type', TopValue::RENTABILITY)->get();

        $date = DateHelper::firstOfMonth($dto->year, $dto->month);

        return [
            'table'         => TopValue::getPivotRentability($dto->year, $dto->month),
            'speedometers'  => $gauges,
            'static_rentability' => TopValue::getRentabilityGauges($date)
        ];
    }
}