<?php

namespace App\Models\Mailing;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property string $name
 * @property string $title
 * @property string $type_of_mailing
 * @property string $days
 * @property string $frequency
 * @property string $status
 * @property int $created_by
 * @property bool $is_template
 * @property string $count
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read MailingNotificationSchedule $schedules
 * @property-read Collection<MailingNotificationSchedule> recipients
 * @method static Builder getTemplates()
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
        'is_template',
        'count'
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
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Получить шаблонные уведомления.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeGetTemplates(Builder $query): Builder
    {
        return $query->where('is_template', 1);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * @return array
     */
    public function mailings(): array
    {
        return json_decode($this->type_of_mailing);
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public static function getById(
        int $id
    ): ?Model
    {
        return self::query()->findOrFail($id);
    }
}
