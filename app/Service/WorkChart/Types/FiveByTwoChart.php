<?php
declare(strict_types=1);

namespace App\Service\WorkChart\Types;

use App\Enums\ErrorCode;
use App\Enums\WorkChart\WorkChartEnum;
use App\Service\WorkChart\Chart;
use App\Support\Core\CustomException;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class FiveByTwoChart implements Chart
{
    /**
     * @param Builder $builder
     * @return bool
     * @throws Exception
     */
    public function chartProcess(Builder $builder): bool
    {
        $countOfDayPerWeek  = $builder->count();
        $today              = Carbon::now();

        if ($today->isSunday() || $today->isSaturday())
        {
            throw new Exception("Сегодня выходные дни. Вы уже достаточно отработали, идите отдыхайте, проведите время с семьей :)");
        }

        if ($countOfDayPerWeek > WorkChartEnum::COUNT_OF_WORK_DAY_FOR_FIVE_BY_TWO)
        {
            throw new Exception("Вы отработали количество рабочих дней для своего графика");
        }

        return true;
    }
}