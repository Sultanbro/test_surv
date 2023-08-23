<?php

namespace App\Service;

use App\Classes\Helpers\Phone;
use App\User;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Http;
use stdClass;

class SendMessageTraineesService
{
    /**
     * @param array $userIds
     * @return bool
     */
    public function handle(array $userIds):bool
    {
        $users = User::query()->where('phone','!=','')->whereIn('id',$userIds)->get();
        foreach($users as $key=>$user)
        {
            $message ='Уважаемый(ая) '.$user->name .' '.$user->last_name.'
Добро пожаловать в нашу большую семью Контакт-Центра "Business Partner" 😀

Вам открыли доступ для обучения и работы в нашем корпоративном портале.
Пройдите по ссылке: bp.jobtron.org

введите логин: '.$user->email.'
введите пароль: 123456

Здесь вы найдете все то, что так не хватает для комфортной работы.
У нас никак у Всех...

Спасибочки за внимание 😉';

            $this->sendNotification($user,$message);
        }

        return true;
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