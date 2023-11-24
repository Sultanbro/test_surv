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
        $users = ProfileGroup::employees($dto->groupId);

        return Position::query()->leftJoin('users as u', 'position.id', '=', 'u.position_id')
            ->whereIn('u.id', $users)->groupBy('position_id')->get(['position_id as id', 'position as name']);}
}