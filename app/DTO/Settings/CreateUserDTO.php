<?php

namespace App\DTO\Settings;

class CreateUserDTO
{
    /**
     * @param int|null $userId
     */
    public function __construct(
        public ?int $userId
    )
    {}

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId ?? null
        ];
    }
}