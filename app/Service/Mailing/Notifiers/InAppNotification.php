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
     * @throws Exception
     */
    public function send(Model $notification, MailingNotificationSchedule $recipient): ?bool
    {
        $recipientType = MailingEnum::TYPES[$recipient->notificationable_type] . 'Notify';

        if (!method_exists(MailingNotificationSchedule::class, $recipientType))
        {
            throw new Exception("Method $recipientType does not exist");
        }

        return (new MailingNotificationSchedule)->{$recipientType}($notification, $recipient);
    }
}