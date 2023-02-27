<?php
declare(strict_types=1);

namespace App\DTO\Admin;

use App\Classes\Helpers\Phone;

final class UpdateAdminDTO
{
    /**
     * @param string|null $name
     * @param string|null $lastName
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $password
     */
    public function __construct(
        public ?string $name,
        public ?string $lastName,
        public ?string $email,
        public ?string $phone,
        public ?string $password
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'last_name' => $this->lastName,
            'email'     => $this->email,
            'phone'     => Phone::normalize($this->phone),
            'password'  => bcrypt($this->password)
        ];
    }
}