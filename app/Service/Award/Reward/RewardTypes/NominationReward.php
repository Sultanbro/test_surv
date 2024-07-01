<?php
declare(strict_types=1);

namespace App\Service\Award\Reward\RewardTypes;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;
use App\Service\Interfaces\Award\Reward\DeleteRewardInterface;
use App\Service\Interfaces\Award\Reward\RewardInterface;
use App\Traits\RewardTrait;
use Illuminate\Http\Request;

final class NominationReward implements RewardInterface, DeleteRewardInterface
{
    use RewardTrait;

    public function __construct(private RewardDTO $rewardDTO)
    {
    }

    /**
     * Наградить сотрудника.
     *
     * @return void
     * @throws \Exception
     */
    public function reward()
    {
        $repository = new AwardRepository();
        $this->rewardUser($this->rewardDTO, $repository);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function deleteReward()
    {
        $repository = new AwardRepository();
        $this->deleteUserReward($this->rewardDTO, $repository);
    }
}