<?php

namespace App\Repositories\Referral;

interface UserStatisticRepositoryInterface
{
    public function getStatistic(array $filter): array;
}
