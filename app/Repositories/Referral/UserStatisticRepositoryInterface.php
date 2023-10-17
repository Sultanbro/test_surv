<?php

namespace App\Repositories\Referral;

interface UserStatisticRepositoryInterface extends StatisticRepositoryInterface
{
    public function getStatistic(array $filter): array;
}
