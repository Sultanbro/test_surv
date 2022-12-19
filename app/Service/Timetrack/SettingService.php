<?php
declare(strict_types=1);

namespace App\Service\Timetrack;

use App\Enums\TimeTrackSettingEnum;
use App\Exceptions\TabException;
use App\KnowBase;
use App\Repositories\PositionRepository;
use App\Repositories\ProfileGroupRepository;
use App\Repositories\UserRepository;
use App\Traits\TimeTrackSetting;
use App\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Класс для работы с Service.
 */
final class SettingService
{
    use TimeTrackSetting;

    private $user;

    private UserRepository $userRepository;

    public function __construct(
        ProfileGroupRepository $profileGroupRepository,
        PositionRepository $positionRepository,
        UserRepository $userRepository
    )
    {
        $this->profileGroupRepository = $profileGroupRepository;
        $this->positionRepository = $positionRepository;
        $this->userRepository = $userRepository;
        $this->user = auth()->user() ?? User::query()->findOrFail(5);
    }

    /**
     * @throws Throwable
     */
    public function handle(string $type)
    {
        try {
            $tabName = $type . 'Tab';

            if (!method_exists($this, $tabName)) {
                throw new TabException("Tab with name $tabName doesn't exist. ");
            }

            return $this->{$tabName}();

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function usersTab(): array
    {
        abort_if(!$this->user->can('users_view'), Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);

        return $this->defaultSettingData([], [
            'users' => (new UserRepository)->getIdFullName(),
            'positions' => (new PositionRepository)->getPositionIdName()
        ]);
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function positionsTab(): array
    {
        abort_if(!$this->user->can('positions_view'),Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);
        return $this->defaultSettingData();
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function groupsTab(): array
    {
        abort_if(!$this->user->can('positions_view'),Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);

        return $this->defaultSettingData(KnowBase::where('parent_id', null)->get());
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function finesTab(): array
    {
        abort_if(!$this->user->can('fines_view'),Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);
        return $this->defaultSettingData();
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function notificationsTab(): array
    {
        abort_if(!$this->user->can('notifications_view'), Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);

        return $this->defaultSettingData([], [
            'users' => $this->userRepository->getIdFullName(),
            'positions' => $this->positionRepository->getPositionIdName()
        ]);
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function permissionsTab(): array
    {
        abort_if(!$this->user->can('permissions_view'), Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);
        return $this->defaultSettingData();
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function checkListsTab(): array
    {
        abort_if(!$this->user->can('checklists_view'), Response::HTTP_FORBIDDEN, TimeTrackSettingEnum::ERROR_MESSAGE_FORBIDDEN);
        return $this->defaultSettingData();
    }
}