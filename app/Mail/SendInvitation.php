<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SendInvitation extends Mailable
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $link
    )
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'jobtron.org'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.send-invitation',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'link' => $this->link
            ]
        );
    }
}
