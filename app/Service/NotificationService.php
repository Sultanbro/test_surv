<?php

namespace App\Service;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\ProfileGroup;
use App\User;
use App\UserNotification;

class NotificationService
{
    /**
     * Получить непрочитанные уведолмения
     * @param Request $request
     * @return UserNotification
     */
    public function getUnreadNotifications(Request $request): UserNotification
    {   
        $user_id = auth()->id();

        $unread_notifications = UserNotification::where('user_id', $user_id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(150)
            ->get();

        foreach($unread_notifications as $item) {

            $message = $item->message;

            $message = str_replace('admin.u-marketing.org', 'bp.jobtron.org', $message);

            $item->message = $message;
        }

        return $unread_notifications;
    }

    /**
     * Получить кол-во непрочитанных уведолмений
     * @return int
     */
    public function countUnreadNotifications(): int
    {   
        $user_id = auth()->id();

        $tec = UserNotification::select(\DB::raw('count(*) as total'))
                    ->where('user_id', $user_id)
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->groupBy('user_id')
                    ->first();

        return $tec ? $tec->total : 0;
    }

    /**
     * Получить прочитанные уведолмения
     * @param Request $request
     * @return UserNotification
     */
    public function getReadNotifications(Request $request): UserNotification
    {   
        $user_id = auth()->id();

        $read_notifications = UserNotification::where('user_id', $user_id)
                    ->where('read_at', '!=' , NULL)
                    ->orderBy('read_at', 'desc')
                    ->take(50)
                    ->get();

        return $read_notifications;
    }

    /**
     * Получить рукводителей для оценки
     * @return array
     */
    public function getHeadUsers(): array
    {   
        return User::withTrashed()
            ->select(
                \DB::raw("CONCAT_WS(' ',ID, last_name, name) as name"),
                'ID as id'
            )
            ->get()
            ->toArray();
    }

    /**
     * Отметить все уведомления прочитанными
     * @return void
     */
    public function setAllRead(): void
    {   

    }

    /**
     * Отметить уведомление прочитанным
     * @return void
     */
    public function setRead(): void
    {   

    }

}