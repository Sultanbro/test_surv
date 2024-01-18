<?php

namespace App\Service\Mailing\Notifiers;

use App\Jobs\WhatsAppNotificationJob;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Throwable;

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
     * @throws Throwable
     */
    public function send(Model $notification, string $message = '', Collection $recipients = null): ?bool
    {
        if ($recipients == null) return false;

        $recipients = $recipients->where('phone', '!=', '');

        $jobs = [];
dd($recipients);
        foreach ($recipients as $recipient) {
            $job = new WhatsAppNotificationJob($recipient, $message);
            $job->delay(now()->addSeconds(2));
            $jobs[] = $job;
        }

        Bus::batch($jobs)->dispatch();

        return true;
    }
}