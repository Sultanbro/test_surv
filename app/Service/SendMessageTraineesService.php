<?php

namespace App\Service;

use App\Console\Commands\ListenQueue;
use App\Jobs\SendNotificationJob;
use App\User;

class SendMessageTraineesService
{
    /**
     * @param array $userIds
     * @return bool
     */
    public function handle(array $userIds):bool
    {
            SendNotificationJob::dispatch($userIds)->delay(now()->addDays(2));

            return true;
    }
}