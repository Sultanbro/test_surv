<?php
declare(strict_types=1);

namespace App\DTO\Premium;

final class EditPremiumDTO
{
    /**
     * @param string $type
     * @param int $userId
     * @param string $amount
     * @param string $comment
     * @param string $date
     */
    public function __construct(
        public string $type,
        public int $userId,
        public string $amount,
        public string $comment,
        public string $date
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            'user_id'   => $this->userId,
            'amount'    => $this->amount,
            'comment'   => $this->comment,
            'date'      => $this->date
        ];
    }
}