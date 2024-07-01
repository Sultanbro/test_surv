<?php

namespace App\Service\Award\Reward\RewardTypes;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;
use App\Service\Interfaces\Award\Reward\DeleteRewardInterface;
use App\Service\Interfaces\Award\Reward\RewardInterface;
use App\Traits\RewardTrait;
use Illuminate\Http\Request;

class CertificateReward implements RewardInterface, DeleteRewardInterface
{
    use RewardTrait;

    public function __construct(private RewardDTO $rewardDTO)
    {
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function reward()
    {
        $repository = new AwardRepository();
        $this->rewardUserWitCourse($this->rewardDTO, $repository);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function deleteReward()
    {
        $repository = new AwardRepository();
        $this->deleteRewardCertificate($this->rewardDTO, $repository);
    }
}