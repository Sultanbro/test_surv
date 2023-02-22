<?php

namespace App\Models\WorkChart;

use App\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workday extends Model
{
    use HasFactory;

    protected $table = 'workdays';

    protected $fillable = [
        'user_id',
        'day_of_week',
        'date',
        'week_number'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param int $userId
     * @param int $dayOfWeek
     * @param string $date
     * @param int $weekNumber
     * @return Model
     * @throws Exception
     */
    public static function createOrFail(
        int $userId,
        int $dayOfWeek,
        string $date,
        int $weekNumber
    ): Model
    {
        $created = self::query()->create([
            'user_id' => $userId,
            'day_of_week' => $dayOfWeek,
            'date'  => $date,
            'week_number' => $weekNumber
        ]);

        if (!$created)
        {
            throw new Exception("При созданий рабочего графика произошла ошибка");
        }

        return $created;
    }
}
