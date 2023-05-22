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
        $check_duplicate_data = WorkChartModel::checkDuplicate($dto);
        if ($check_duplicate_data) {
            throw new Exception('Данная запись уже существует');
        }
        $updated = WorkChartModel::query()->findOrFail($dto->id)->update($dto->toArray());

        if (!$updated)
        {
            throw new Exception("При обновлений $dto->name произошла ошибка");
        }

        return $updated;
    }
}