<?php

namespace App\Jobs\Registration;

use App\Exceptions\Regisration\CantSendRegistrationEmailException;
use App\Mail\PortalCreatedMail;
use App\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessSendPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly User   $user,
        private readonly string $password
    )
    {
        $this->onQueue('mail');
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws CantSendRegistrationEmailException
     */
    public function handle(): void
    {

        $mail = new PortalCreatedMail([
            'name' => $this->user->name,
            'password' => $this->password,
        ]);

        try {
            Mail::to($this->user->email)->send($mail);
        } catch (Exception $e) {
            throw new CantSendRegistrationEmailException(
                'Failed to send mail to ' . $this->user->email . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
