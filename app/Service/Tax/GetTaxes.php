<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\User;

/**
* Класс для работы с Service.
*/
class GetTaxes
{
    public function handle(
        int $userId
    )
    {
        return User::getUserById($userId)->taxes->toArray();
    }
}