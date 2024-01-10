<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\UpdateTaxDTO;
use App\Models\Tax;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class UpdateTaxService
{
    /**
     * @throws Exception
     */
    public function handle(
        UpdateTaxDTO $dto
    ): bool
    {
        try {
            $tax = Tax::getTaxById($dto->id);

            DB::transaction(function () use ($tax, $dto){

                $tax?->update([
                    'name'       => $dto->name,
                    'end_subtraction' => $dto->end_subtraction
                ]);

                $tax?->users()
                    ->where('user_id', $dto->userId)
                    ->update([
                        'is_percent' => $dto->isPercent,
                        'value' => $dto->value
                    ]);
            });

            return true;

        }catch (Throwable $exception)
        {
            throw new Exception("При обновлений $dto->id произошла ошибка");
        }
    }
}
