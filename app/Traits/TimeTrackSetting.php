<?php

namespace App\Traits;

use App\Models\Books\BookGroup;
use App\Repositories\PositionRepository;
use App\Repositories\ProfileGroupRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait TimeTrackSetting
{
    public function __construct(
        public ProfileGroupRepository $profileGroupRepository,
        public PositionRepository $positionRepository
    )
    {

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function defaultSettingData($corpBooks = [], $userAndNotificationTabs = []): array
    {
        return [
            'groups'            => $this->profileGroupRepository->getGroupsIdNameWithPluck(true),
            'archivedGroups'    => $this->profileGroupRepository->getGroupsIdNameWithPluck(),
            'positions'         => $this->positionRepository->getPositionIdNameWithPluck(),
            'bookGroups'        => BookGroup::all(),
            'groupsWithId'      => $this->profileGroupRepository->getGroupsIdName(),
            'corpBooks'         => $corpBooks,
            'userAndNotificationTabs' => $userAndNotificationTabs
        ];
    }
}