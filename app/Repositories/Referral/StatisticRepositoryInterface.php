<?php

namespace App\Repositories\Referral;

use App\User;

interface StatisticRepositoryInterface
{
    public function getStatistic(array $filter): array;
}
