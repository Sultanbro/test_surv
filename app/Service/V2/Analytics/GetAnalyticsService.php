<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\Analytics\Analytics;
use App\Facade\Analytics\AnalyticsFacade;
use App\Helpers\DateHelper;
use App\Models\Analytics\AnalyticStat;
use App\Traits\AnalyticTrait;

/**
* Класс для работы с Service.
*/
class GetAnalyticsService
{
    use AnalyticTrait;

    /**
     * @param GetAnalyticDto $dto
     * @return array
     */
    public function handle(
        GetAnalyticDto $dto
    ): array
    {
        $date = DateHelper::firstOfMonth($dto->year, $dto->month);

        $columns = $this->columns($date, $dto->groupId)->map(function ($column){
            return [
                'key' => $column->name
            ];
        });
        dd(AnalyticsFacade::analytics($dto));
        return [
            'table' => AnalyticStat::form($dto->groupId, $date),
            'columns' => $columns
        ];
    }
}