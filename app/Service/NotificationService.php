<?php

namespace App\Service;

use App\DTO\BaseDTO;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\ProfileGroup;
use App\User;
use App\UserNotification;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Получить непрочитанные уведолмения
     * @param Request $request
     * @return Collection
     */
    public function getUnreadNotifications(Request $request): Collection
    {   
        $user_id = auth()->id();

        $unread_notifications = UserNotification::where('user_id', $user_id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(150)
            ->get();

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
     * @return Collection
     */
    public function getReadNotifications(Request $request): Collection
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
     * Отметить уведомление прочитанным.
     *
     * @param int $userNotificationId
     * @return Model<UserNotification>
     */
    public function setRead(
        int $userNotificationId
    ): Model
    {
        $userNotification = UserNotification::getById($userNotificationId);

        if (!isset($userNotification->read_at))
        {
            $userNotification->update([
                'read_at' => now()
            ]);
        }

        return $userNotification;
    }

    /**
     * @param int $userId
     * @return int
     */
    public function unRead(
        int $userId
    ): int
    {
        return UserNotification::getByUserId($userId)->whereNull('read_at')->count();
    }
}