<?php

namespace App\Service\Mailing\Notifiers;

use Illuminate\Database\Eloquent\Model;

class BitrixNotification implements Notification
{
    public function send(Model $notification): bool
    {
        dd('Bitrix');
    }
}