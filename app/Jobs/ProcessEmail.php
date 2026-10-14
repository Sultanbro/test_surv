<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notification;

class ProcessEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email, $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, Notification $notification)
    {
        $this->email = $email;
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $email = $this->email;
        $id = $this->notification->id;
        $title = $this->notification->title;
        $message = $this->notification->message;
        $image = $this->notification->image;


        $template = 'admin.notification_email';
        $subject = 'Новое уведомление.';
        $data = [
            'title' => $title,
            'message' => $message,
            'image' => $image,
        ];

        \Mail::to($email)->send(new \App\Mail\Mail($template, $subject, $data));

        Notification::find($id)->increment('emails_sent');
    }
}
