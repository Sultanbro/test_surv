<?php

namespace App\Service\Mailing\Notifiers;

use App\Models\Mailing\MailingNotificationSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Notification
{
    public function send(Model $notification, string $message = '', Collection $recipients = null): ?bool;
}