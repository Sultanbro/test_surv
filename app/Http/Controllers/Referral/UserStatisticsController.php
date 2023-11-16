<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\StatisticRequest;
use App\Http\Resources\Referral\StatisticResource;
use App\Repositories\Referral\UserStatisticRepositoryInterface;
use App\Service\Referral\Core\StatisticDto;
use App\User;

class UserStatisticsController extends Controller
{
    public function __construct(
        private readonly UserStatisticRepositoryInterface $repository
    )
    {
    }

    public function __invoke(StatisticRequest $request, ?User $user): StatisticResource
    {
        $statistic = new StatisticDto(
            $this->repository->statistic($request->validated(), $user)
        );
        return StatisticResource::make($statistic);
    }
}
