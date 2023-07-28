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

class RejectOvertimeService
{
    public function handle($data){
        $user = User::findOrFail($data['user_id']);
        $groupId =  $user->inGroups()->first()->id;
        $groupHead = GroupUser::getHeadInGroup($groupId);

        Redis::del($groupId."_".$data['user_id']);
        UserNotification::changeStatus($groupHead->user_id);
        return true;
    }
}