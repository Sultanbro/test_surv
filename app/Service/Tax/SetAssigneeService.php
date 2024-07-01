<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\SetUserTaxDTO;
use App\DTO\Tax\SetAssignedTaxDTO;
use App\Models\Tax;
use App\Models\UserTax;
use App\User;
use DB;
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
}
