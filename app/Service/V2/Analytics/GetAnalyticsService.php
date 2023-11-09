<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticDto;
use App\Facade\Analytics\AnalyticsFacade;
use App\Helpers\DateHelper;
use App\Traits\AnalyticTrait;

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

        $columns = $this->columns($date, $dto->groupId)->map(function ($column) {
            return [
                'key' => $column->name
            ];
        });
        return [
            'table' => AnalyticsFacade::analytics($dto),
            'columns' => $columns
        ];
    }
}