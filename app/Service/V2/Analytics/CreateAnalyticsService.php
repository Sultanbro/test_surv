<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\CreateAnalyticDto;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateAnalyticsService
{
    /**
     * @param CreateAnalyticDto $dto
     * @return bool
     * @throws Throwable
     */
    public function handle(CreateAnalyticDto $dto): bool
    {
        try {
            DB::beginTransaction();

            AnalyticColumn::createAnalyticsColumns($dto);
            AnalyticRow::createAnalyticsRows($dto);
            Activity::createQuality($dto->groupId);

            DB::commit();

            return true;
        } catch (Throwable $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}