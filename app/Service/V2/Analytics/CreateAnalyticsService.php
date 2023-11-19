<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\CreateAnalyticDto;
use App\Helpers\DateHelper;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateAnalyticsService
{
    /**
     * @param CreateAnalyticDto $dto
     * @return void
     * @throws Throwable
     */
    public function handle(CreateAnalyticDto $dto): void
    {
        try {
            DB::beginTransaction();

            AnalyticColumn::createAnalyticsColumns($dto);
            AnalyticRow::createAnalyticsRows($dto);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}