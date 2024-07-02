<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Types;

use App\Enums\ErrorCode;
use App\Enums\WorkChart\WorkChartEnum;
use App\Service\WorkChart\Chart;
use App\Support\Core\CustomException;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class OneByOneChart implements Chart
{
    /**
     * @param Builder $builder
     * @return bool
     * @throws Exception
     */
    public function chartProcess(Builder $builder): bool
    {
        $countOfDayPerWeek = $builder->count();

        if ($countOfDayPerWeek >= WorkChartEnum::COUNT_OF_WORK_DAY_FOR_ONE_BY_ONE)
        {
            throw new Exception("Вы отработали количество рабочих дней для своего графика");
        }

        return true;
    }
}