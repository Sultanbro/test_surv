<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\Analytics\AnalyticsFacade;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\UserStat;
use App\Models\Kpi\Bonus;
use App\QualityRecordWeeklyStat;
use App\Repositories\ActivityRepository;
use App\Traits\AnalyticTrait;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
final class GetActivitiesService
{
    use AnalyticTrait;

    /**
     * Тип показателя коллекций.
     */
    const COLLECTION = 'collection';

    /**
     * Тип показателя по умолчанию.
     */
    const DEFAULT = 'default';

    /**
     * Тип показателя качество.
     */
    const QUALITY = 'quality';

    public function handle(GetAnalyticDto $dto)
    {
        return AnalyticsFacade::activitiesViews(
            $dto->groupId,
            [Activity::VIEW_DEFAULT, Activity::VIEW_COLLECTION, Activity::VIEW_QUALITY]
        )->map(function ($activity) use ($dto){
            $date           = DateHelper::firstOfMonth($dto->year, $dto->month);
            $workdays       = $this->workdays($activity->weekdays, $dto->year, $dto->month);
            $plan           = (new ActivityRepository)->getDailyPlan($activity, $dto->year, $dto->month) ?? null;
            $activity->plan = $plan->plan ?? $activity->daily_plan;
            $activity->workdays = $workdays;

            /**
             * Types of activities.
             */
            if ($activity->type == self::COLLECTION)
            {
                $collection         = $this->collection($activity, $date, $dto->groupId);
                $activity->price    = $collection['price'];
                $activity->records  = $collection['records'];
            }

            if ($activity->type == self::DEFAULT)
            {
                $activity->records = AnalyticsFacade::userStatisticFormTable($activity, $date, $dto->groupId);
            }

            if ($activity->type == self::QUALITY)
            {
                $quality = $this->quality($dto, $activity);
                $activity->records  = $quality['records'];
                $activity->weeks    = $quality['weeks'];
            }

            return $activity;
        });
    }

    /**
     * @param GetAnalyticDto $dto
     * @param Activity $activity
     * @return array
     */
    private function quality(
        GetAnalyticDto $dto,
        Activity $activity
    ): array
    {
        $date       = DateHelper::firstOfMonth($dto->year, $dto->month);
        $employees  = $this->employees($dto->groupId, $date)->whereDoesntHave('activities')->withWhereHas('weekQualities',
            fn($quality) => $quality->where('month', $dto->month)->where('year', $dto->year)->select('day', 'total', 'user_id'))->get();

        return [
            'records'   => $employees,
            'weeks'     => QualityRecordWeeklyStat::weeksArray($dto->month, $dto->year)
        ];
    }

    /**
     * @param Activity $activity
     * @param string $date
     * @param int $groupId
     * @return array
     */
    private function collection(
        Activity $activity,
        string $date,
        int $groupId
    ): array
    {
        $bonus = Bonus::where('activity_id', $activity->id)->first();

        return [
            'price'     => $bonus?->sum ?? 0,
            'records'   => AnalyticsFacade::userStatisticFormTable($activity, $date, $groupId)
        ];
    }

    /**
     * @param int $workdays
     * @param int $year
     * @param int $month
     * @return int
     */
    private function workdays(
        int $workdays,
        int $year,
        int $month
    ): int
    {
        $weekdays   = [0, 6, 5, 4, 3, 2, 1];
        $ignore     = array_slice($weekdays, 0, -$workdays);

        return workdays($year, $month, $ignore);
    }
}