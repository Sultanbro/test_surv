<?php
declare(strict_types=1);

namespace App\DTO\GroupUser;

final class GetUsersDTO
{
    /**
     * @param int|null $id
     */
    public function __construct(
        public ?int $id
    )
    {}
}