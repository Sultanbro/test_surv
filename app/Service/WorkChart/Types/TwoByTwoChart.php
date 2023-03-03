<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Types;

use App\Enums\ErrorCode;
use App\Enums\WorkChart\WorkChartEnum;
use App\Service\WorkChart\Chart;
use App\Support\Core\CustomException;
use Illuminate\Database\Eloquent\Builder;

class TwoByTwoChart implements Chart
{
    /**
     * @param Builder $builder
     * @return bool
     */
    public function chartProcess(Builder $builder): bool
    {
        $countOfDayPerWeek = $builder->count();

        if ($countOfDayPerWeek >= WorkChartEnum::COUNT_OF_WORK_DAY_FOR_TWO_BY_TWO)
        {
            new CustomException("Вы отработали количество рабочих дней для своего графика", ErrorCode::BAD_REQUEST, []);
        }

        return true;
    }
}