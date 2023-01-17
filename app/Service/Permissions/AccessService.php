<?php

namespace App\Service\Permissions;

use App\DTO\Permissions\SwitchAccessDTO;
use App\Enums\ErrorCode;
use App\Repositories\Permissions\PermissionRepository;
use App\Support\Core\CustomException;
use Exception;
use Illuminate\Support\Collection;

/**
* Класс для работы с Service.
*/
class AccessService
{
    public function __construct(
        private PermissionRepository $permissionRepository
    )
    {}

    public function handle(SwitchAccessDTO $dto): void
    {
//        try {
            $names = $this->getNames($dto->accesses);
            $ids = $this->getIds($dto->accesses);

            $permissions = $this->permissionRepository->multiFindByNameOrId(
                names: $names, ids: $ids
            );

            dd($permissions);
//        } catch (Exception $exception) {
//            new CustomException('При переключений произошла ошибка', ErrorCode::BAD_REQUEST, []);
//        }
    }

    /**
     * @param array $permissions
     * @return array
     */
    private function getNames(
        array $permissions
    ): array
    {
        return collect($permissions)->map(
            function ($permission) {
                return $permission['name'];
            }
        )->toArray();
    }

    /**
     * @param array $permissions
     * @return array
     */
    private function getIds(
        array $permissions
    ): array
    {
        return collect($permissions)->map(
            function ($permission) {
                return $permission['id'];
            }
        )->toArray();
    }
}