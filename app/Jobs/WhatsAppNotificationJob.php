<?php

namespace App\Jobs;

use App\Classes\Helpers\Phone;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class WhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    private string $token;
    private string $phone;
    private string $message;
    private string $channel_id;
    private string $url = "https://api.wazzup24.com/v3/message";

    public function __construct($user, $message)
    {
        $this->token = config('wazzup')['token'];
        $this->channel_id = config('wazzup')['channel_id'];
        $this->phone = Phone::normalize($user->phone);
        $this->message = $message;
    }

    public function handle(): void
    {
        $this->sendNotification($this->phone, $this->message);
    }


    /**
     * @param $phone
     * @param string $message
     * @return void
     */
    private function sendNotification(
        $phone,
        string $message
    ): void
    {
        Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer $this->token"
        ])
            ->timeout(100)
            ->post($this->url, [
                'channelId' => $this->channel_id,
                'chatId' => $phone,
                'text' => $message,
                'chatType' => 'whatsapp',
            ]);
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping($this->phone))->dontRelease()];
    }
}
