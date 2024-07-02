<?php

namespace App\Service\Admin;

use App\DTO\Admin\AddOrUpdateAdminDTO;
use App\Setting;
use App\Traits\UploadFileS3;
use App\User;
use Exception;

/**
* Класс для работы с Service.
*/
class UpdateAdminService
{
    use UploadFileS3;

    /**
     * @param User $user
     * @param AddOrUpdateAdminDTO $dto
     * @return bool
     */
    public function handle(
        User $user,
        AddOrUpdateAdminDTO $dto
    ): bool
    {
        $fileName = "";
        if ($dto->image) {
            try {
                $fileName = $this->uploadFile('admins/images', $dto->image);
            } catch (Exception $e) {}
        }


        $s = Setting::query()->where('name', Setting::DEFAULT_MANAGER)->first();
        if ($s) {
            $s->update(['value' => $user->id]);
        } else {
            Setting::query()->create([
                'name' => Setting::DEFAULT_MANAGER,
                'description' => 'it is default manager for owners',
                'value' => $user->id
            ]);
        }

        if ($dto->password) {
            $user->password = $dto->password;
        }
        $user->name = $dto->name;
        $user->last_name = $dto->lastName;
        $user->email = $dto->email;
        $user->role_id = $dto->roleId;
        $user->img_url = $fileName;
        $user->phone = $dto->phone;
        $user->update();

        if ($dto->roleId == 0) {
            $user->removeRole(1);
        } else {
            $user->assignRole(1);
        }


        return true;
    }
}
