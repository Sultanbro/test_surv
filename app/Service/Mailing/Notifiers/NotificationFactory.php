<?php
declare(strict_types=1);

namespace App\Service\Mailing\Notifiers;

use DB;
use InvalidArgumentException;
use Throwable;

class NotificationFactory
{
    /**
     * @throws Throwable
     */
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