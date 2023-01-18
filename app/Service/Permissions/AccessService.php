<?php

namespace App\Service\Permissions;

use App\DTO\Permissions\SwitchAccessDTO;
use App\Enums\ErrorCode;
use App\Repositories\Permissions\PermissionRepository;
use App\Repositories\UserRepository;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
* Класс для работы с Service.
*/
class AccessService
{
    public function __construct(
        private UserRepository $userRepository
    )
    {}

    /**
     * @param SwitchAccessDTO $dto
     * @return int
     */
    public function handle(SwitchAccessDTO $dto): int
    {
        try {
            $user = auth()->user() ?? User::query()->find(5);

            if (!$user)
            {
                new CustomException('Доступ закрыт', ErrorCode::BAD_REQUEST, []);
            }

            if (!$this->userRepository->switchPermission($dto->userId, $dto->accesses))
            {
                new CustomException('Issue when try attach or update data to permission_user', ErrorCode::BAD_REQUEST, []);
            }

            return Response::HTTP_CREATED;

        } catch (Exception $exception) {
            new CustomException('При переключений произошла ошибка', ErrorCode::BAD_REQUEST, []);
        }
    }
}