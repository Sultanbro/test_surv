<?php

namespace App\Service\Mailing\Notifiers;

use App\Jobs\WhatsAppNotificationJob;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;

class WhatsAppNotification implements Notification
{
    public function __construct()
    {
    }

    /**
     * @param Model $notification
     * @param string $message
     * @param Collection|null $recipients
     * @return bool|null
     * @throws Exception
     */
    public function send(Model $notification, string $message = '', Collection $recipients = null): ?bool
    {
        if ($recipients == null) {
            return false;
        } else {
            $recipients = $recipients->where('phone', '!=', '');

            foreach ($recipients as $recipient) {
                WhatsAppNotificationJob::dispatch($recipient, $message)->delay(1);
            }

            return true;
        }
    }
}