<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $tenantId = tenant('id');

        $mailData['host'] = $host = $tenantId
            ? $tenantId .'.' . config('app.domain')
            : config('app.domain');

        $mailData['hostname'] = 'https://' . $host;

        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.password-reset')
            ->subject('Восстановления пароля');
    }
}
