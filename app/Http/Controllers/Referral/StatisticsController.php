<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\FilterRequest;
use App\Http\Resources\Referral\StatisticResource;
use App\Repositories\Referral\StatisticRepositoryInterface;
use App\Repositories\Referral\UserStatisticRepositoryInterface;
use App\Service\Referral\Core\StatisticDto;
use App\User;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly StatisticRepositoryInterface     $statisticRepository,
        private readonly UserStatisticRepositoryInterface $userStatisticRepository,
    )
    {
    }

    public function referrers(FilterRequest $request): StatisticResource
    {
        $statistic = new StatisticDto(
            $this->statisticRepository->statistic($request->validated())
        );
        return StatisticResource::make($statistic);
    }

    public function referrer(FilterRequest $request, ?User $user): StatisticResource
    {
        $statistic = new StatisticDto(
            $this->userStatisticRepository->statistic($request->validated(), $user)
        );
        return StatisticResource::make($statistic);
    }
}
