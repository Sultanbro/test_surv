<?php

namespace App\Service\Interfaces\Award\Reward;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;

interface RewardBuilderInterface
{
    public function handle(RewardDTO $rewardDTO, AwardRepository $repository);
}