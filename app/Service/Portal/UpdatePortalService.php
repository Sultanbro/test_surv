<?php
declare(strict_types=1);

namespace App\Service\Portal;

use App\DTO\Portal\UpdatePortalDTO;
use App\Models\Portal\Portal;
use Exception;

/**
* Класс для работы с Service.
*/
class UpdatePortalService
{
    /**
     * @param UpdatePortalDTO $dto
     * @return bool
     * @throws Exception
     */
    public function handle(
        UpdatePortalDTO $dto
    ): bool
    {
        //TODO implement saving kpiBacklight as json-field in portal table

        $updated = Portal::query()
            ->get()
            ->where('tenant_id', $dto->tenantId)
            ->firstOrFail()
            ->update($dto->toArray());

        if (!$updated)
        {
            throw new Exception("При обновлений портала произошла ошибка");
        }

        return $updated;
    }
}