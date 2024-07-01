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

    protected $userIds;

    /**
     * @param User $userIds
     */
    public function __construct(array $userIds)
    {
        $this->userIds = $userIds;
    }

    /**
     * @return void
     * @throws HttpClientException
     */
    public function handle():void
    {
        $users = User::query()->where('phone','!=','')->whereIn('id',$this->userIds)->get();
        foreach($users as $key=>$user) {
            $message = 'Уважаемый(ая) ' . $user->name . ' ' . $user->last_name . '

Добро пожаловать в нашу большую семью Контакт-Центра "Business Partner" 😀

Вам открыли доступ для обучения и работы в нашем корпоративном портале.

Пройдите по ссылке: https://bp.jobtron.org
Ведите логин: ' . $user->email . '
Введите пароль: 12345

Здесь Вы найдете все то, что нужно для комфортной работы.

У нас никак у Всех 😉
Спасибочки за внимание.';

            $this->delay(now()->addMinutes($key+1))->sendNotification($user, $message);
        }
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
