<?php

namespace App\Service\Mailing\Notifiers;

use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\Models\Mailing\MailingNotificationSchedule;
use App\UserNotification;
use Carbon\Carbon;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InAppNotification implements Notification
{
    /**
     * @param Model $notification
     * @param string $message
     * @param Collection|null $recipients
     * @return bool|null
     */
    public function send(Model $notification, string $message = '', Collection $recipients = null): ?bool
    {
        $recipients = $recipients ?? MailingFacade::getRecipients($notification->id);
        $recipients = $recipients->pluck('id')->toArray();

        foreach ($recipients as $recipient)
        {
            UserNotification::createNotification($notification->name, $message, $recipient);
        }

        return true;
    }
}