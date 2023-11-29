<?php

namespace App\Jobs;

use App\Classes\Helpers\Phone;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use stdClass;

class WhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    /**
     * @var string
     */
    private string $token;

    private $users;

    private string $message;

    public function __construct($users,$message)
    {
        $this->token = config('wazzup')['token'];
        $this->users = $users;
        $this->message = $message;
    }

    public function handle()
    {
            $this->sendNotification($this->users,$this->message);;
    }



    /**
     * @param User|stdClass $user
     * @param string $message
     * @return void
     * @throws HttpClientException
     */
    private function sendNotification(
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
            ->timeout(100)
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
