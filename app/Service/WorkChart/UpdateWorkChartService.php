<?php
declare(strict_types=1);

namespace App\Service\WorkChart;

use App\DTO\WorkChart\UpdateWorkChartDTO;
use App\Models\WorkChart\WorkChartModel;
use Exception;

/**
* Класс для работы с Service.
*/
class UpdateWorkChartService
{
    /**
     * @param UpdateWorkChartDTO $dto
     * @return bool
     * @throws Exception
     */
    public function handle(
        UpdateWorkChartDTO $dto
    ): bool
    {
        $updated = WorkChartModel::query()->findOrFail($dto->id)->update($dto->toArray());

        if (!$updated)
        {
            throw new Exception("При обновлений $dto->name произошла ошибка");
        }

        return $updated;
    }
}