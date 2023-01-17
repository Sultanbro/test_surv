<?php

namespace App\Service\Timetrack\Group;

use App\DTO\GroupUser\SaveUsersDTO;
use App\ProfileGroup;
use App\Repositories\ProfileGroupRepository;
use App\Traits\GroupUserTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT;
use Throwable;

/**
* Класс для работы с Service.
*/
class StoreUserService
{
    /**
     * @param ProfileGroupRepository $profileGroupRepository
     */
    public function __construct(
        private ProfileGroupRepository $profileGroupRepository
    )
    {}

    /**
     * @throws Throwable
     */
    public function handle(
        SaveUsersDTO $dto
    ): array
    {
        try {
            $group = $this->profileGroupRepository->profileGroupWithRelation($dto->groupId, ['dialer']);

            $this->profileGroupRepository->updateGroupData($group, $dto->groupInfo);


            if (isset($dto->dialerId))
            {
                $this->profileGroupRepository->updateOrCreateDialer($group->id, $dto->dialerId, $dto->scriptId, $dto->talkHours, $dto->talkMinutes);
            }

            return [
                'groups' => $this->profileGroupRepository->getGroupsIdNameWithPluck(true),
                'group'  => $group
            ];

        } catch (Throwable $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}