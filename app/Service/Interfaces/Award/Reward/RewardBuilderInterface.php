<?php

namespace App\Service\Interfaces\Award\Reward;

interface RewardBuilderInterface
{
    public function handle(string $type);
}