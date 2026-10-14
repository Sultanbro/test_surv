<?php

namespace App\Repositories\Signature;

use App\User;

interface SignatureHistoryRepositoryInterface
{
    public function addHistory(User $user, array $data);
}