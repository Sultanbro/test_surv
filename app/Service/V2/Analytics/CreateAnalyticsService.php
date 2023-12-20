<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\CreateAnalyticDto;
use App\Helpers\DateHelper;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\ProfileGroup;
use Carbon\Carbon;
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

            $date = Carbon::createFromDate($dto->year, $dto->month, 1)->format('Y-m-d');

            AnalyticColumn::defaults($dto->groupId, $date);
            AnalyticRow::defaults($dto->groupId, $date);

            ProfileGroup::query()->findOrFail($dto->groupId)->update([
                'has_analytics' => 1
            ]);

            Activity::createQuality($dto->groupId);

            DB::commit();

            return true;
        } catch (Throwable $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}