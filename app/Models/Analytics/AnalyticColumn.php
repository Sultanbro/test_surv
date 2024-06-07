<?php

namespace App\Models\Analytics;

use App\DTO\Analytics\V2\CreateAnalyticDto;
use App\Helpers\DateHelper;
use App\Models\Scopes\AnalyticColumnScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $group_id
 * @property string $name
 * @property string $date
 * @property string $order
 */
class AnalyticColumn extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'date',
        'order',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new AnalyticColumnScope);
    }

    public static function defaults($group_id, $date): void
    {
        $fields = ['name', 'sum', 'avg'];

        foreach ($fields as $index => $field) {
            self::query()->create([
                'group_id' => $group_id,
                'name' => $field,
                'date' => $date,
                'order' => $index + 1,
            ]);
        }

        $dateStart = Carbon::parse($date)->startOfMonth();
        $dateEnd = Carbon::parse($date)->endOfMonth();
        while ($dateStart <= $dateEnd) {
            self::query()->create([
                'group_id' => $group_id,
                'name' => $dateStart->day,
                'date' => $date,
                'order' => $dateStart->day + 4,
            ]);
            $dateStart->addDay();
        }
    }

    /**
     * @param CreateAnalyticDto $dto
     * @return void
     */
    public static function createAnalyticsColumns(CreateAnalyticDto $dto): void
    {
        $date = Carbon::createFromDate($dto->year, $dto->month)->firstOfMonth();
        $daysInMonth = DateHelper::daysInMonthToArray($dto->year, $dto->month);
        $fields = [
            'name',
            'sum',
            'avg',
        ];
        $columns = array_merge($fields, $daysInMonth);

        $insertionValues = [];

        foreach ($columns as $index => $column) {
            $insertionValues[] = [
                'group_id' => $dto->groupId,
                'name' => $column,
                'date' => $date->toDateString(),
                'order' => $index,
            ];
        }

        self::query()->insert($insertionValues);
    }

    public static function getValuesBetweenDates($group_id, $start_date, $end_date)
    {
        return self::whereBetween('date', [$start_date, $end_date])
            ->where('group_id', $group_id)
            ->get()
            ->toArray();
    }
}
