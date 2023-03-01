<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Types;

use App\Enums\ErrorCode;
use App\Enums\WorkChart\WorkChartEnum;
use App\Service\WorkChart\Chart;
use App\Support\Core\CustomException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class FiveByTwoChart implements Chart
{
    /**
     * @param Builder $builder
     * @return bool
     */
    public function chartProcess(Builder $builder): bool
    {
        $countOfDayPerWeek  = $builder->count();
        $today              = Carbon::now();

        if ($today->isSunday() || $today->isSaturday())
        {
            new CustomException("Сегодня выходные дни. Вы уже достаточно отработали, идите отдыхайте, проведите время с семьей :)", ErrorCode::BAD_REQUEST, []);
        }

        if ($countOfDayPerWeek >= WorkChartEnum::COUNT_OF_WORK_DAY_FOR_FIVE_BY_TWO)
        {
            new CustomException("Вы отработали количество рабочих дней для своего графика", ErrorCode::BAD_REQUEST, []);
        }

        return true;
    }
}