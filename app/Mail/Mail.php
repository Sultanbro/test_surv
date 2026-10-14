<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $template, $subject, $data, $from;
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $subject, $data, $from = null)
    {
        $this->template = $template;
        $this->subject = $subject;
        $this->data = $data;
        $this->from = $from;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        if(!is_null($this->from)) {
            $this->markdown($this->template)
                ->from($this->from)
                ->subject($this->subject)
                ->with($this->data);
        } else {
            // Надо будет указать 'from' в config/mail.php
            $this->markdown($this->template)
                ->subject($this->subject)
                ->with($this->data);
        }

    }
}
