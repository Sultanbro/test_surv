<?php
declare(strict_types=1);

namespace App\DTO\Notification;

use App\DTO\BaseDTO;

final class SetReadDTO extends BaseDTO
{
    /**
     * @param int $userNotificationId
     */
    public function __construct(
        public int $userNotificationId
    )
    {}

    /**
     * @return int[]
     */
    public function toArray(): array
    {
        return [
            'user_notification_id' => $this->userNotificationId,
        ];
    }
}