<?php

namespace App\Models\Mailing;

use App\Enums\Mailing\MailingEnum;
use App\Traits\Notificationable;
use App\UserNotification;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $notificationable_id
 * @property string $notificationable_type
 * @property int $notification_id
 * @property array $days
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class MailingNotificationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'notificationable_id',
        'notificationable_type',
        'notification_id',
        'days'
    ];

    /**
     * @return BelongsTo
     */
    public function mailingNotification(): BelongsTo
    {
        return $this->belongsTo(MailingNotification::class, 'notification_id');
    }

    public function notificationable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param MailingNotification $notification
     * @param MailingNotificationSchedule $schedule
     * @return void
     */
    public function individualNotify(MailingNotification $notification, MailingNotificationSchedule $schedule): void
    {
        UserNotification::createNotification($notification->name, $notification->title, $schedule->notificationable_id);
    }

    /**
     * @param MailingNotification $notification
     * @param MailingNotificationSchedule $schedule
     * @return void
     */
    public function groupNotify(MailingNotification $notification, MailingNotificationSchedule $schedule): void
    {
        $userIds = $schedule->notificationable->activeUsers()->get()->pluck('id')->toArray();

        foreach ($userIds as $userId)
        {
            UserNotification::createNotification($notification->name, $notification->title, $userId);
        }
    }

    /**
     * @param MailingNotification $notification
     * @param MailingNotificationSchedule $schedule
     * @return void
     */
    public function positionNotify(MailingNotification $notification, MailingNotificationSchedule $schedule): void
    {
        $userIds = $schedule->notificationable->users()->pluck('id')->toArray();

        foreach ($userIds as $userId)
        {
            UserNotification::createNotification($notification->name, $notification->title, $userId);
        }
    }
}
