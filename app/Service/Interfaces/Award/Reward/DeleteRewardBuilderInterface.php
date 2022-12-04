<?php

namespace App\Service\Interfaces\Award\Reward;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;

interface DeleteRewardBuilderInterface
{
    public function execute(RewardDTO $rewardDTO, AwardRepository $repository);
}