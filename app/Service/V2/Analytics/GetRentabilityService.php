<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetRentabilityDto;
use App\Helpers\DateHelper;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use Carbon\Carbon;
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
        $date = Carbon::create($dto->year, $dto->month);

        $gauges = TopValue::query()
            ->whereHas('groups', function (Builder $group) use ($date) {
                $group->whereIn('has_analytics', [ProfileGroup::HAS_ANALYTICS, ProfileGroup::ARCHIVED])
                    ->whereNotIn('id', [ProfileGroup::BUSINESS_CENTER_ID, ProfileGroup::IT_DEPARTMENT_ID])
                    ->when($date->isCurrentMonth(), fn($query) => $query->where('active', ProfileGroup::IS_ACTIVE))
                    ->where(function (Builder $q) use ($date) {
                        $q->where(fn($q) => $q
                            ->whereNull('archived_date')
                            ->where('active', ProfileGroup::IS_ACTIVE));
                        $q->orWhere(fn($q) => $q
                            ->whereDate('archived_date', '>=', $date->format("Y-m-d"))
                        );
                    });
            })
            ->where('type', TopValue::RENTABILITY)
            ->get();


        return [
            'table' => TopValue::getPivotRentability($dto->year, $dto->month),
            'speedometers' => $gauges,
            'static_rentability' => TopValue::getRentabilityGauges($date->format("Y-m-d"))
        ];
    }
}