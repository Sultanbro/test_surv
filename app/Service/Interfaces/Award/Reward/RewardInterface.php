<?php

namespace App\Service\Interfaces\Award\Reward;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;
use Illuminate\Http\Request;

interface RewardInterface
{
    public function reward();
}