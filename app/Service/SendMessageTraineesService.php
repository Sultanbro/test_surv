<?php

namespace App\Service;

use App\Console\Commands\ListenQueue;
use App\Jobs\SendNotificationJob;
use App\User;

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
Пройдите по ссылке: https://bp.jobtron.org/

введите логин: '.$user->email.'
введите пароль: 12345

Здесь вы найдете все то, что так не хватает для комфортной работы.
У нас никак у Всех...

Спасибочки за внимание 😉';


            SendNotificationJob::dispatch($user, $message)->delay(now()->addMinutes($key+1));
        }
        return true;
    }
}