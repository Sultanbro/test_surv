<?php

namespace App\Service\Awards\Reward\RewardTypes;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;
use App\Service\Interfaces\Award\Reward\RewardInterface;
use App\Traits\RewardTrait;
use Illuminate\Http\Request;

class CertificateReward implements RewardInterface
{
    use RewardTrait;

    public function __construct(private RewardDTO $rewardDTO)
    {
    }

    public function reward()
    {
        $repository = new AwardRepository();
        $this->rewardUserWitCourse($this->rewardDTO, $repository);
    }
}