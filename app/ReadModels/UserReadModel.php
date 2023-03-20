<?php

namespace App\ReadModels;

use App\User;

class UserReadModel
{
    public function getUserTaxes(
        int $userId
    )
    {
        return User::query()->where('id', $userId)->leftJoin('user_tax as ut', '');
    }
}