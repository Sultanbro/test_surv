<?php

namespace App\Models\Mailing;

use App\Traits\Notificationable;
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
    use HasFactory, Notificationable;

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
}
