<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\EditActivityDto;
use App\Models\Analytics\Activity;
use App\Service\AnalyticService;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class EditActivityService
{
    /**
     * @param EditActivityDto $dto
     * @return bool
     * @throws Throwable
     */
    public function handle(EditActivityDto $dto): bool
    {
        DB::beginTransaction();

        $activity = Activity::query()->findOrFail($dto->activity['id']);

        /**
         * Обновление плана для группы.
         */
        (new AnalyticService)->updatePlanPerMonth(
            $activity,
            (string) $dto->activity['daily_plan'],
            $dto->activity['plan_unit'],
            $dto->year,
            (string) $dto->month,
        );

        /**
         * Обновление значений.
         */
        $activity->update([
            'name'      => $dto->activity['name'],
            'plan_unit' => $dto->activity['plan_unit'],
            'unit'      => $dto->activity['unit'],
            'weekdays'  => $dto->activity['weekdays'],
        ]);


        if (isset($dto->employees))
        {
            $activity->users()->attach($dto->employees);
        }

        DB::commit();

        return true;
    }
}