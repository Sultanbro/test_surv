<?php
declare(strict_types=1);

namespace App\DTO\Admin;

use Illuminate\Http\UploadedFile;

final class AddOrUpdateAdminDTO
{
    /**
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string $password
     * @param int|null $roleId
     * @param UploadedFile|null $image
     * @param string $phone
     * @param bool $isDefault
     */
    public function __construct(
        public string $name,
        public string $lastName,
        public string $email,
        public string $password,
        public ?int $roleId,
        public ?UploadedFile $image,
        public string $phone,
        public bool $isDefault
    )
    {}

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'last_name' => $this->lastName,
            'email'     => $this->email,
            'password'  => $this->password,
            'role_id'   => $this->roleId,
            'image'     => $this->image,
            'phone'     => $this->phone,
            'is_default'=> $this->isDefault,
        ];
    }
}
