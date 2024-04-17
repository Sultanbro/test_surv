<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\UserNotification;
use App\User;

class NotificationService
{
    /**
     * Получить непрочитанные уведолмения
     * @return Collection
     */
    public function getUnreadNotifications(): Collection
    {
        $user_id = auth()->id();

        return UserNotification::query()->where('user_id', $user_id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->take(150)
            ->get();
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
     * @return Collection
     */
    public function getReadNotifications(): Collection
    {
        $user_id = auth()->id();

        return UserNotification::query()->where('user_id', $user_id)
                    ->where('read_at', '!=' , NULL)
                    ->orderBy('read_at', 'desc')
                    ->take(50)
                    ->get();
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
