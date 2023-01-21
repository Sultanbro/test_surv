<?php
declare(strict_types=1);

namespace App\Service\Timetrack;

use App\ProfileGroup;
use App\Repositories\ProfileGroupRepository;
use App\User;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
* Класс для работы с Service.
*/
final class GroupService
{
    /**
     * @param ProfileGroupRepository $profileGroupRepository
     */
    public function __construct(
        public ProfileGroupRepository $profileGroupRepository
    )
    {}

    /**
     * @return object
     * @throws \Exception
     */
    public function getGroups(): object
    {
        try {
            $user = auth()->user() ?? User::query()->findOrFail(18);
            $groups = $this->profileGroupRepository->getActive();
            if ($user->id != User::OWNER_ID)
            {
                $groups = $this->profileGroupRepository->checkEditor($user->id);
            }

            return $groups;
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * Создать группу.
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function store(
        string $name
    )
    {
        try {
            return ProfileGroup::query()->create([
                'name' => $name,
                'editors_id' => '[]'
            ]);
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(
        int $id
    ): bool
    {
        try {
            return $this->profileGroupRepository->deactivateGroup($id);
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     *  Возвращает true если у группы статус обновился false если у группы уже имеется статус активный
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function retoreGroup(
        int $id
    )
    {
        try {
            return $this->profileGroupRepository->restoreOrIgnore($id);
        } catch (\Throwable $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}