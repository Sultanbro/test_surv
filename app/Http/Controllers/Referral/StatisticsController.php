<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Http\Requests\Referral\StatisticRequest;
use App\Http\Resources\Referral\StatisticResource;
use App\Repositories\Referral\StatisticRepositoryInterface;
use App\Service\Referral\Core\StatisticDto;

class StatisticsController extends Controller
{
    public function __construct(
        private readonly StatisticRepositoryInterface $repository
    )
    {
    }

    public function __invoke(StatisticRequest $request): StatisticResource
    {
        $statistic = new StatisticDto(
            $this->repository->getStatistic($request->validated())
        );
        return StatisticResource::make($statistic);
    }
}
