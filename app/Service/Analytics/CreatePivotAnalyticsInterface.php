<?php

namespace App\Service\Analytics;

interface CreatePivotAnalyticsInterface
{
    public function create(?int $groupId): void;
}