<?php

namespace App\Listeners;

use App\Events\EmailNotificationEvent;
use App\Mail\SendInvitation;
use Exception;
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
    public function handle(EmailNotificationEvent $event): void
    {
        Mail::to($event->email)
            ->send(new SendInvitation(
                $event->name,
                $event->email,
                $event->password,
                $event->link
            ));
    }
}
