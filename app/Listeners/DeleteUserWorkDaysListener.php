<?php

namespace App\Listeners;

use App\Events\DeleteUserWorkDays;
use App\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteUserWorkDaysListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param DeleteUserWorkDays $event
     * @return void
     * @throws Exception
     */
    public function handle(DeleteUserWorkDays $event): void
    {
        if (!$event->user->workdays()->detach())
        {
            throw new Exception("При удалений рабочих дней у пользователя $event->user->full_name произошла ошибка");
        }
    }
}
