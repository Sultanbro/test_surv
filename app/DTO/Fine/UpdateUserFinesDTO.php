<?php
declare(strict_types=1);

namespace App\DTO\Fine;

use App\DTO\BaseDTO;

final class UpdateUserFinesDTO extends BaseDTO
{
    /**
     * @param int $userId
     * @param array $fines
     * @param string $date
     * @param string $comment
     */
    public function __construct(
        public int $userId,
        public array $fines,
        public string $date,
        public string $comment,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'userId'   => $this->userId,
            'fines'    => $this->fines,
            'date'     => $this->date,
            'comment'  => $this->comment,
        ];
    }
}