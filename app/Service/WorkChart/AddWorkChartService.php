<?php
declare(strict_types=1);

namespace App\Service\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use App\Models\WorkChart\WorkChartModel;
use Exception;
use Throwable;

/**
* Класс для работы с Service.
*/
class AddWorkChartService
{
    /**
     * @throws Exception
     */
    public function handle(StoreWorkChartDTO $dto): \Illuminate\Database\Eloquent\Model
    {
        try {
           return WorkChartModel::createModel($dto);
        } catch (Throwable $exception)
        {
            throw new Exception($exception->getMessage());
        }
    }
}