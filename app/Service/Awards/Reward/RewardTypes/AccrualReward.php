<?php

namespace App\Service\Awards\Reward\RewardTypes;

use App\Service\Interfaces\Award\Reward\RewardInterface;
use App\Traits\RewardTrait;

class AccrualReward implements RewardInterface
{
    use RewardTrait;

    public function reward()
    {
        // TODO: Implement reward() method.
    }
}