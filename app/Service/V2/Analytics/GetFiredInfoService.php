<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetFiredInfoDto;
use App\ProfileGroup;
use App\Service\AnalyticService;
use Carbon\Carbon;

/**
* Класс для работы с Service.
*/
class GetFiredInfoService
{
    /**
     * @var AnalyticService
     */
    private AnalyticService $analyticService;

    public function __construct()
    {
        $this->analyticService = new AnalyticService;
    }

    public function handle(GetFiredInfoDto $dto): array
    {
        $group = ProfileGroup::findOrFail($dto->groupId);
        $date  = Carbon::createFromDate($dto->year, $dto->month)->firstOfMonth();

        return [
            'fired_percent_prev'    => $this->analyticService->getFiredUsersPerMonthPercent($group, $date->subMonth()),
            'fired_percent'         => $this->analyticService->getFiredUsersPerMonthPercent($group, $date->addMonth()),
            'fired_number_prev'     => $this->analyticService->getFiredUsersPerMonth($group, $date->subMonth()),
            'fired_number'          => $this->analyticService->getFiredUsersPerMonth($group, $date->addMonth()),
        ];
    }
}