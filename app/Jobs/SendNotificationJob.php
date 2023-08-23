<?php

namespace App\Jobs;

use App\Classes\Helpers\Phone;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use stdClass;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $message;

    public function __construct(User $user, string $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * @return void
     * @throws HttpClientException
     */
    public function handle():void
    {
        $this->sendNotification($this->user, $this->message);
    }
    /**
     * @param User|stdClass $user
     * @param string $message
     * @return void
     * @throws HttpClientException
     */
    public function sendNotification(
        User|stdClass $user,
        string $message
    ): void
    {
        $phone      = Phone::normalize($user->phone);
        $channelId  = config('wazzup')['channel_id'];
        $token  = config('wazzup')['token'];

        $response = Http::withHeaders([
            "Content-Type"  => "application/json",
            "Authorization" => "Bearer $token"
        ])
            ->timeout(10)
            ->post("https://api.wazzup24.com/v3/message", [
                'channelId' => $channelId,
                'chatId'    => $phone,
                'text'      => $message,
                'chatType'  => 'whatsapp',
            ]);

        if (!$response->successful())
        {
            throw new HttpClientException($response->body());
        }
    }

}