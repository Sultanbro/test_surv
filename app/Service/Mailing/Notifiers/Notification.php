<?php

namespace App\Service\Mailing\Notifiers;

use App\Models\Mailing\MailingNotificationSchedule;
use Illuminate\Database\Eloquent\Model;

interface Notification
{
    public function send(Model $notification, MailingNotificationSchedule $recipient): ?bool;
}