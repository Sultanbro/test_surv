<?php
declare(strict_types=1);

namespace App\Service\Admin;

use App\Classes\Helpers\Phone;
use App\DTO\Admin\AddAdminDTO;
use App\Enums\ErrorCode;
use App\Helpers\FileHelper;
use App\Support\Core\CustomException;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
* Класс для работы с Service.
*/
class AddAdminService
{
    /**
     * @param AddAdminDTO $dto
     * @return Model
     */
    public function handle(
        AddAdminDTO $dto
    ): Model
    {
        $fileName = FileHelper::save($dto->image, 'admins/images');

        return User::query()->create([
            'name'      => $dto->name,
            'lat_name'  => $dto->lastName,
            'email'     => $dto->email,
            'phone'     => Phone::normalize($dto->phone),
            'img_url'     => $fileName,
            'password'  => $dto->password
        ]);
    }
}