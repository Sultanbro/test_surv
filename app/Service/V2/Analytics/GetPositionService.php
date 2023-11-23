<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\GetAnalyticPositionsDto;
use App\Position;
use App\ProfileGroup;
use App\User;
use Illuminate\Database\Eloquent\Collection;

/**
* Класс для работы с Service.
*/
class GetPositionService
{
    /**
     * @param GetAnalyticPositionsDto $dto
     * @return Collection
     */
    public function handle(GetAnalyticPositionsDto $dto): Collection
    {
        $users = \App\ProfileGroup::employees($dto->groupId);

        $positions = Position::query()->withWhereHas('users', fn ($users) => $users->select('id')->whereIn('id', $users))->get();
        return $positions;
    }
}