<?php

namespace App\Models\Mailing;

use App\Enums\Mailing\MailingEnum;
use App\UserNotification;
use Exception;
use Illuminate\Database\Eloquent\Builder;
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
        'notification_id'
    ];

    /**
     * @return BelongsTo
     */
    public function mailingNotification(): BelongsTo
    {
        return $this->belongsTo(MailingNotification::class, 'notification_id');
    }

    /**
     * @return MorphTo
     */
    public function notificationable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param string $type
     * @param int $id
     * @param int $notificationId
     * @return Builder|Model
     */
    public static function create(
        string $type,
        int $id,
        int $notificationId
    ): Builder|Model
    {
        return self::query()->create([
            'notificationable_type' => $type,
            'notificationable_id'   => $id,
            'notification_id'       => $notificationId
        ]);
    }
}
