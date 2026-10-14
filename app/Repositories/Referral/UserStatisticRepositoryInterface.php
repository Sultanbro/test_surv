<?php

namespace App\Repositories\Referral;

use App\User;

interface UserStatisticRepositoryInterface extends StatisticRepositoryInterface
{
    public function statistic(array $filter, ?User $user = null): array;
}
