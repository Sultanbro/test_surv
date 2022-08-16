<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messenger', function () {
    return true;
});
