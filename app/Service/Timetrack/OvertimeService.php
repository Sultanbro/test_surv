<?php

namespace App\Service\Timetrack;

use App\Models\GroupUser;
use App\UserNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OvertimeService
{
    public function handle($data){
        $user = Auth::user();
        $groupId =  $user->inGroups()->first()->id;

        $groupHead = GroupUser::getHeadInGroup($groupId);
        if (!$groupHead) throw new Exception("Руководитель группы не найден");

        $checkRequest = Redis::get($groupId."_".$user->id);
        if ($checkRequest) throw new Exception("Вы уже отправили заявку на этот день");

        UserNotification::changeStatus($groupHead->user_id);

        $notification = DB::transaction(function () use ($groupHead, $data, $user) {
            try{
                $create = UserNotification::createNotification(
                    'Заявка на сверхурочную работу',
                    $user->name.' '.$user->last_name.' оставил заявление о приеме на сверхурочную работу в указанный день: '.$data['date'],
                    $groupHead->user_id,
                    $user->id
                );
                return $create;
            } catch (\Exception $e){
                return false;
            }
        });

        if (!$notification) throw new Exception("Заявка не отправлена");
        Redis::set($groupId."_".$user->id , $data['date']);
        Redis::expire($groupId."_".$user->id , 60*60*2);

        return $notification;
    }
}