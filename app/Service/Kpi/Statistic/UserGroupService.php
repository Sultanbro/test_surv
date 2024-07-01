<?php
declare(strict_types=1);

namespace App\Service\Kpi\Statistic;

use App\DTO\Kpi\Statistic\UserGroupDTO;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
* Класс для работы с Service.
*/
class UserGroupService
{
    /**
     * @param UserGroupDTO $dto
     * @return Collection|null
     */
    public function handle(
        UserGroupDTO $dto
    ): ?Collection
    {
        $user = User::getUserById($dto->userId);

        return $user->groups()
            ->whereYear('group_user.from', $dto->year)
            ->whereMonth('group_user.from', $dto->month)
            ->get(['name']) ?? null;
    }
}