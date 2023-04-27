<?php

namespace App\Service\Mailing\Notifiers;

use App\Enums\Mailing\MailingEnum;
use App\Facade\MailingFacade;
use App\Models\Mailing\MailingNotification;
use App\UserNotification;
use Carbon\Carbon;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

class InAppNotification implements Notification
{
    public function send(Model $notification): bool
    {
        $recipients = $notification->schedules;

        foreach ($recipients as $recipient)
        {
            match ($notification->frequency) {
                MailingEnum::WEEKLY  => $recipient->weekly(),
                MailingEnum::MONTHLY => $recipient->monthly(),
                MailingEnum::DAILY   => $recipient->daily(),
                default => throw new InvalidArgumentException('Undefined')
            };
        }
        return true;
    }
}