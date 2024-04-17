<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;
    public string $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, string $password)
    {
        $mailData['host'] = config('app.domain');

        $mailData['hostname'] = 'https://' . config('app.domain');

        $this->mailData = $mailData;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        return $this->view('mail.portal-created')
            ->subject('Портал создан');
    }
}
