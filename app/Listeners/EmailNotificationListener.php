<?php

namespace App\Listeners;

use App\Events\EmailNotificationEvent;
use App\Mail\SendInvitation;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailNotificationListener
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
     * @param EmailNotificationEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(EmailNotificationEvent $event)
    {
        try {
            Mail::to($event->email)->send(new SendInvitation([
                'name'      => $event->name,
                'authData' => $event->authData,
            ]));
        } catch (Throwable $e) {
            throw new Exception('Ошибка при отправке сообщений на электронную почту');
        }
    }
}
