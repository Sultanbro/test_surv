<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\SpeedometerDto;
use App\Models\Analytics\TopValue;
use App\Models\Analytics\UserStat;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class RentabilitySpeedometerService
{
    /**
     * @param SpeedometerDto $dto
     * @return array
     * @throws Throwable
     */
    public function handle(SpeedometerDto $dto): array
    {
        DB::beginTransaction();

        $gauge = $dto->gauge;
        $topValue = TopValue::query()->updateOrCreate([
            'id' => $gauge['id']
        ], $dto->toArray());

        if ($topValue->issetActivities())
        {
            UserStat::total_for_month($topValue->activity_id, $topValue->date, $topValue->value_type);
        }

        $options = TopValue::getDynamicValue($topValue->group_id, $topValue->date, $topValue)['options'];

        DB::commit();

        return [
            'options' => $options
        ];
    }
}