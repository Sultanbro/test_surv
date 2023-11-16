<?php

namespace App\Repositories\Referral;

interface StatisticRepositoryInterface
{
    public function statistic(array $filter): array;
}
