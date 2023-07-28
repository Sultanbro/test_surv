<?php

namespace App\Service\Timetrack;

use App\Http\Controllers\Timetrack\TimetrackingController;
use App\Models\GroupUser;
use App\UserNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AcceptOvertimeService
{
    public function handle($data){
        $groupHead = GroupUser::getHeadInGroup($data['group_id']);
        $checkRequest = Redis::get($data['group_id']."_".$data['user_id']);
        if (!$checkRequest) {
            UserNotification::changeStatus($groupHead->user_id);
            throw new Exception("Срок заявки истек");
        }

        Redis::del($data['group_id']."_".$data['user_id']);

        return true;
    }
}