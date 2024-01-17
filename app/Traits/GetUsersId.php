<?php

namespace App\Traits;

use App\ProfileGroup;
use App\User;

trait GetUsersId
{
    /**
     * Get employee ids which should notified by using notification_type and notification_id
     */
    private function getUserIds($recipients): array
    {
        $employeeIds = [];

        foreach ($recipients as $item) {

            if ($item->notificationable_type == 'App\\User') {
                $employeeIds[] = $item->notificationable_id;
            } elseif ($item->notificationable_type == 'App\\ProfileGroup') {
                $userIds = ProfileGroup::getById($item->notificationable_id)
                    ->activeUsers()
                    ->pluck('user_id')
                    ->toArray();
                $employeeIds = array_merge($employeeIds, $userIds);
            } elseif ($item->notificationable_type == 'App\\Position') {
                $userIds = User::query()->where('position_id', $item->notificationable_id)->pluck('id')->toArray();
                $employeeIds = array_merge($employeeIds, $userIds);
            }
        }
        return array_unique($employeeIds);
    }
}