<?php

namespace App\Service;

use App\Jobs\SendNotificationJob;
use App\User;
use Illuminate\Http\Client\HttpClientException;

class SendMessageTraineesService
{
    /**
     * @param array $userIds
     * @return bool
     * @throws HttpClientException
     */
    public function handle(array $userIds)
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

            SendNotificationJob::dispatch($user, $message)->delay(now()->addMinutes($key+0.5));

        }
        exec('php artisan queue:work --stop-when-empty');

        return true;
    }
}