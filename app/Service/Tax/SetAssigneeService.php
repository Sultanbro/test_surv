<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\UserTaxDTO;
use App\DTO\Tax\SetAssignedTaxDTO;
use App\Models\Tax;
use App\Models\UserTax;
use App\User;
use Exception;
use Throwable;

/**
 * Класс для работы с Service.
 */
class SetAssigneeService
{
    /**
     * @param SetAssignedTaxDTO $dto
     * @return bool
     * @throws Exception
     */
    public function handle(
        SetAssignedTaxDTO $dto
    ): bool
    {
        try {
            $user = User::getUserById($dto->userId);
            $tax = Tax::getTaxById($dto->taxId);

            if (!$user->taxes->contains($dto->taxId) && $dto->isAssigned) {
                $tax->users()->attach($dto->userId);
            } else {
                $tax->users()->detach($dto->userId);
            }

            return true;
        } catch (Throwable $exception) {
            throw new Exception("При указаний налога для пользователя $dto->userId произошла ошибка");
        }
    }


    public function attach(UserTaxDTO $dto): bool
    {
        $exist = UserTax::query()
            ->where('user_id', $dto->userId)
            ->where('tax_id', $dto->taxId)
            ->whereNull('to')
            ->exists();

        if (!$exist) {
            UserTax::query()->create([
                'user_id' => $dto->userId,
                'tax_id' => $dto->taxId,
                'is_percent' => $dto->taxId,
                'end_subtraction' => $dto->endSubtraction,
                'value' => $dto->value,
                'status' => UserTax::ACTIVE,
                'from' => now()->toDateString()
            ]);
        }

        return true;
    }

    public function detach(UserTaxDTO $dto): bool
    {
        $exist = UserTax::query()
            ->where('user_id', $dto->userId)
            ->where('tax_id', $dto->taxId)
            ->whereNull('to')
            ->exists();

        if ($exist) {
            UserTax::query()
                ->where('user_id', $dto->userId)
                ->where('tax_id', $dto->taxId)
                ->whereNull('to')
                ->update([
                    'status' => UserTax::REMOVED,
                    'to' => now()->toDateString()
                ]);
        }

        return true;
    }
}
