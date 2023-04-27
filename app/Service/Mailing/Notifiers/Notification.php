<?php

namespace App\Service\Mailing\Notifiers;

use Illuminate\Database\Eloquent\Model;

interface Notification
{
    public function send(Model $notification): bool;
}