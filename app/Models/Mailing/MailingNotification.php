<?php

namespace App\Models\Mailing;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $type_of_mailing
 * @property string $frequency
 * @property string $time
 * @property int $status
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read MailingNotificationSchedule $schedules
 */
class MailingNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type_of_mailing',
        'frequency',
        'time',
        'status',
        'created_by'
    ];

    /**
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(MailingNotificationSchedule::class, 'notification_id');
    }
}
