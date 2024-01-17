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

class WhatsAppNotificationJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public int $uniqueFor = 1800; // The job is unique for 0.5 hour

    /**
     * @var string
     */
    private string $token;

    private string $phone;

    private string $message;

    public function __construct($user, $message)
    {
        $this->token = config('wazzup')['token'];
        $this->phone = Phone::normalize($user->phone);
        $this->message = $message;
    }

    /**
     * Set Job unique with this identifier
     */
    public function uniqueId()
    {
        return $this->phone . $this->message;
    }

    /**
     * @throws HttpClientException
     */
    public function handle()
    {
        $this->sendNotification($this->phone, $this->message);;
    }


    /**
     * @param $phone
     * @param string $message
     * @return void
     * @throws HttpClientException
     */
    private function sendNotification(
        $phone,
        string $message
    ): void
    {
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
