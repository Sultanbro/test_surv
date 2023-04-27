<?php
declare(strict_types=1);

namespace App\Service\Mailing\Notifiers;

use InvalidArgumentException;

class NotificationFactory
{
    public static function createNotification(
        string $type
    ): Notification
    {
        return match ($type) {
            'in-app' => new InAppNotification(),
            'bitrix' => new BitrixNotification(),
            default => throw new InvalidArgumentException("Invalid notification type"),
        };
    }
}