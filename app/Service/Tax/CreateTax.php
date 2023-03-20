<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\CreateTaxDTO;
use App\Models\Tax;
use Exception;
use Illuminate\Database\Eloquent\Builder;

/**
* Класс для работы с Service.
*/
class CreateTax
{
    /**
     * @param CreateTaxDTO $dto
     * @return Builder
     * @throws Exception
     */
    public function handle(
        CreateTaxDTO $dto
    ): Builder
    {
        try {
            return Tax::query()->create($dto->toArray());
        } catch (\Throwable $exception) {
            throw new Exception('При созданий налога произошла ошибка');
        }
    }
}