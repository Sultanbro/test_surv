<?php
declare(strict_types=1);

namespace App\DTO\Fine;

use App\DTO\BaseDTO;

final class UpdateUserFinesDTO extends BaseDTO
{
    /**
     * @param int $user_id
     * @param array $fines
     * @param string $date
     * @param string $comment
     */
    public function __construct(
        public int $user_id,
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
            'user_id'   => $this->user_id,
            'fines'      => $this->fines,
            'date'     => $this->date,
            'comment'     => $this->comment,
        ];
    }
}