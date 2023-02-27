<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\DTO\Admin\UpdateAdminDTO;
use App\Enums\ErrorCode;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use Throwable;

/**
* Класс для работы с Service.
*/
class UpdateAdminService
{
    /**
     * @param User $user
     * @param UpdateAdminDTO $dto
     * @return bool
     */
    public function handle(
        User $user,
        UpdateAdminDTO $dto
    ): bool
    {
        return $user->update($dto->toArray());
    }
}