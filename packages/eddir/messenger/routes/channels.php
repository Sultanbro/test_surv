<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-messages.{userId}', function ($user, $userId) {
    return true;
});

