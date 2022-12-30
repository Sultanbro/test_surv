<?php

namespace App\Listeners;

use App\Events\EmailNotificationEvent;
use App\Mail\SendInvitation;
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
     */
    public function handle(EmailNotificationEvent $event)
    {
        try {
            Mail::to($event->email)->send(new SendInvitation([
                'name'      => $event->name,
                'email'     => $event->email,
                'password'  => $event->password,
                'subdomain' => tenant('id')
            ]));
        } catch (Throwable $e) {
            return redirect()->to('/timetracking/create-person')->withInput()->withErrors('Возможно вы ввели не верный email или его не существует! <br><br> ' . $e->getMessage());
        }
    }
}
