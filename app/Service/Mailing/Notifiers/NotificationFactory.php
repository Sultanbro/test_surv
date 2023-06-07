<?php
declare(strict_types=1);

namespace App\Service\Mailing\Notifiers;

use App\Enums\Mailing\MailingEnum;
use DB;
use InvalidArgumentException;
use Throwable;

class NotificationFactory
{
    /**
     * Для того чтоб создать новую интеграцию с сервисом создайте новый класс по пути
     * App\Service\Mailing\Notifiers и пишем там всю логику.
     *
     * @throws Throwable
     */
    public static function createNotification(
        string $type
    ): Notification
    {
        return match ($type) {
            'in-app'    => new InAppNotification(),
            'bitrix'    => new BitrixNotification(),
            'whatsapp'  => new WhatsAppNotification(),
            default     => throw new InvalidArgumentException("Invalid notification type"),
        };
    }
}