<?php

namespace App\Models\Mailing;

use App\User;
use Illuminate\Database\Eloquent\Builder;
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
        'days',
        'frequency',
        'status',
        'created_by',
        'is_template'
    ];

    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';

    /**
     * @return HasMany
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(MailingNotificationSchedule::class, 'notification_id');
    }

    /**
     * Получить шаблонные уведомления.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGetTemplates(Builder $query)
    {
        return $query->where('is_template', 1);
    }

    /**
     * @return array
     */
    public function mailings(): array
    {
        return json_decode($this->type_of_mailing);
    }
}
