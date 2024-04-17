<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\Classes\Helpers\Phone;
use App\DTO\Admin\AddOrUpdateAdminDTO;
use App\Enums\ErrorCode;
use App\Helpers\FileHelper;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class AddAdminService
{
    /**
     * @param AddOrUpdateAdminDTO $dto
     * @return Model
     * @throws Throwable
     */
    public function handle(
        AddOrUpdateAdminDTO $dto
    ): Model
    {
        $fileName = "";
        try {
            $fileName = $this->uploadFile('admins/images', $dto->image);
        } catch (Exception) {}

        $user = User::getByEmail($dto->email)->exists();

        if ($user)
        {
            new CustomException("Электронная почта $dto->email уже существует", ErrorCode::BAD_REQUEST, []);
        }

        return DB::transaction(function () use ($dto, $fileName) {
            $user =  User::query()->create([
                'name'      => $dto->name,
                'last_name'  => $dto->lastName,
                'email'     => $dto->email,
                'phone'     => Phone::normalize($dto->phone),
                'img_url'   => $fileName,
                'password'  => $dto->password
            ]);

            $user->roles()->attach($dto->roleId);

            return  $user;
        });
    }

    private function uploadFile(String $path, $file)
    {
        $disk = \Storage::disk('s3');

        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '_' . md5((string) time()) . '.' . $extension;

        $disk->putFileAs($path, $file, $fileName);

        return $path . '/' . $fileName;
    }
}
