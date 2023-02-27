<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\DTO\Admin\AddAdminDTO;
use App\Enums\ErrorCode;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class AddAdminService
{
    public function handle(
        AddAdminDTO $dto
    )
    {
        dd($dto);
    }
}