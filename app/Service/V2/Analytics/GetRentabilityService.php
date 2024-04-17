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
        $gauges = ProfileGroup::withRentability($dto->year, $dto->month)
            ->map(fn($group) => TopValue::query()->where('group_id', $group->id)->where('type', 2)->get())
            ->filter(fn($group) => $group->count() > 0)->toArray();
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);

        return [
            'table' => TopValue::getPivotRentability($dto->year, $dto->month),
            'speedometers' => $gauges,
            'static_rentability' => TopValue::getRentabilityGauges($date)
        ];
    }
}