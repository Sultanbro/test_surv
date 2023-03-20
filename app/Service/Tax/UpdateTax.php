<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\UpdateTaxDTO;
use App\Models\Tax;
use Exception;
use Throwable;

/**
* Класс для работы с Service.
*/
class UpdateTax
{
    /**
     * @throws Exception
     */
    public function handle(
        UpdateTaxDTO $dto
    ): bool
    {
        try {
            return Tax::query()->findOrFail($dto->id)->update([
                'name' => $dto->name,
                'value' => $dto->value,
                'is_percent' => $dto->isPercent
            ]);
        }catch (Throwable $exception)
        {
            throw new Exception("При обновлений $dto->id произошла ошибка");
        }
    }
}