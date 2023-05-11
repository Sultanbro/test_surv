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

class InAppNotification implements Notification
{
    /**
     * @param Model $notification
     * @param string $message
     * @return bool|null
     * @throws Exception
     */
    public function send(Model $notification, string $message = ''): ?bool
    {
        $recipients = MailingFacade::getRecipients($notification->id)->pluck('id')->toArray();

        foreach ($recipients as $recipient)
        {
            UserNotification::createNotification($notification->name, $message, $recipient);
        }

        return true;
    }
}