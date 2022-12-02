<?php
declare(strict_types=1);

namespace App\Service\Awards\Reward;

use App\DTO\RewardDTO;
use App\Enums\AwardTypeEnum;
use App\Exceptions\News\BusinessLogicException;
use App\Repositories\AwardRepository;
use App\Service\Awards\Reward\RewardTypes\AccrualReward;
use App\Service\Awards\Reward\RewardTypes\CertificateReward;
use App\Service\Awards\Reward\RewardTypes\NominationReward;
use App\Service\Interfaces\Award\Reward\RewardBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class RewardBuilder implements RewardBuilderInterface
{
    /**
     * @throws BusinessLogicException
     */
    public function handle(RewardDTO $rewardDTO, AwardRepository $repository)
    {
        $award = $repository->getById($rewardDTO->awardId);

        if (!$award->category()->exists())
        {
            throw new BusinessLogicException("Category for award, $award does not exists");
        }

        $typeName = AwardTypeEnum::VALUES[$award->category->type];
        $method = $typeName . 'AwardTypeReward';

        if (!method_exists($this, $method)) {
            throw new BusinessLogicException("Method $method does not exists");
        }

        return $this->{$method}($rewardDTO);

    }

    /**
     * @param $rewardDTO
     * @return NominationReward
     */
    public function nominationAwardTypeReward($rewardDTO): NominationReward
    {
        return new NominationReward($rewardDTO);
    }

    /**
     * @param $rewardDTO
     * @return AccrualReward
     */
    public function accrualAwardTypeReward($rewardDTO): AccrualReward
    {
        return new AccrualReward($rewardDTO);
    }

    /**
     * @param $rewardDTO
     * @return CertificateReward
     */
    public function certificateAwardTypeReward($rewardDTO): CertificateReward
    {
        return new CertificateReward($rewardDTO);
    }
}