<?php

namespace App\Service\Mailing\Notifiers;

use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class InAppNotification implements Notification
{
    public function send(Model $notification): bool
    {
        $recipients = $notification->schedules;

        foreach ($recipients as $recipient)
        {
            if ($notification->frequency == MailingEnum::WEEKLY)
            {
                $recipient->weekly();
            }

            if ($notification->frequency == MailingEnum::MONTHLY)
            {
                $recipient->monthly();
            }
        }
        return true;
    }
}