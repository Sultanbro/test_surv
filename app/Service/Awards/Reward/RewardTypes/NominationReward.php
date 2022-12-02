<?php
declare(strict_types=1);

namespace App\Service\Awards\Reward\RewardTypes;

use App\DTO\RewardDTO;
use App\Repositories\AwardRepository;
use App\Service\Interfaces\Award\Reward\RewardInterface;
use App\Traits\RewardTrait;
use Illuminate\Http\Request;

final class NominationReward implements RewardInterface
{
    use RewardTrait;

    public function __construct(private RewardDTO $rewardDTO)
    {
    }

    public function reward()
    {
        $repository = new AwardRepository();
        $this->rewardUser($this->rewardDTO, $repository);
    }
}