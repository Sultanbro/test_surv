<?php

namespace App\Service\Timetrack;

use App\Http\Controllers\Timetrack\TimetrackingController;
use App\Models\GroupUser;
use App\User;
use App\UserNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AcceptOvertimeService
{
    public function handle($data){
        $user = User::findOrFail($data['user_id']);
        $groupId =  $user->inGroups()->first()->id;
        $groupHead = GroupUser::getHeadInGroup($groupId);
        $checkRequest = Redis::get($groupId."_".$data['user_id']);
        if (!$checkRequest) {
            UserNotification::changeStatus($groupHead->user_id);
            throw new Exception("Срок заявки истек");
        }

        Redis::del($groupId."_".$data['user_id']);

        return true;
    }
}