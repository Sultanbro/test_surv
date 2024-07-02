<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\CreateTaxDTO;
use App\Models\Tax;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
* Класс для работы с Service.
*/
class CreateTaxService
{
    /**
     * @param CreateTaxDTO $dto
     * @return Model
     * @throws Exception
     */
    public function handle(
        CreateTaxDTO $dto
    ): Model
    {
        try {
            return Tax::query()->create($dto->toArray());
        } catch (\Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}