<?php

namespace App\Models\Mailing;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
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
        'name',
        'title',
        'type_of_mailing',
        'frequency',
        'time',
        'status',
        'created_by'
    ];

    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';

    /**
     * @return HasMany
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(MailingNotificationSchedule::class, 'notification_id');
    }
}
