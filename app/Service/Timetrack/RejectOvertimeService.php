<?php

namespace App\Service\Timetrack;

use App\Http\Controllers\Timetrack\TimetrackingController;
use App\Models\GroupUser;
use App\UserNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class RejectOvertimeService
{
    public function handle($data){
        $groupHead = GroupUser::getHeadInGroup($data['group_id']);

        Redis::del($data['group_id']."_".$data['user_id']);
        UserNotification::changeStatus($groupHead->user_id);
        return true;
    }
}