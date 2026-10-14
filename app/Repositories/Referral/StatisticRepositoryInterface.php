<?php

namespace App\Repositories\Referral;

use App\User;

interface StatisticRepositoryInterface
{
    public function statistic(array $filter): array;
}
