<?php

namespace App\Models\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use App\Enums\WorkChart\WorkChartEnum;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class WorkChartModel extends Model
{
    use HasFactory;

    protected $table = 'work_charts';

    //TODO: refactor: replace 'text_name' with 'name'
    // refactor name to chartWorkdays and chartDayoffs fields
    protected $fillable = [
        'name',
        'text_name',
        'start_time',
        'end_time',
        'work_charts_type'
    ];

    /**
     * @param string $name
     * @return Model
     * @throws Exception
     */
    public static function getByNameOrFail(
        string $name
    ): Model
    {
        $chart = self::query()->where('name', $name)->first();

        if ($chart == null)
        {
            throw new Exception("В базе данных нет графика по названию $name");
        }

        return $chart;
    }

    /**
     * @throws Exception
     */
    public static function deleteByOne(
        int $id
    )
    {
        $deleted = self::query()->when($id, fn($query) => $query->where('id', $id))->delete();

        if (!$deleted)
        {
            throw new \Exception("При удалений $id произошла ошибка");
        }

        return $deleted;
    }

    /**
     * @param StoreWorkChartDTO $dto
     * @return Model
     */
    public static function createModel(StoreWorkChartDTO $dto): Model
    {
        return WorkChartModel::query()->create($dto->toArray());
    }

    /**
     * Получаем рабочие дни для разных типов графика.
     *
     * @param User $user
     * @return int
     */
    public static function workdaysPerMonth(
        User $user
    ): int
    {
        $date = Carbon::now();
        $chart = $user->activeGroup()?->workChart()?->first();

        if($user->workChart()->exists())
        {
            $chart = $user->workChart()->first();
        }

        $igonore = isset($chart->name) && in_array($chart->name, ['1-1', '2-2', '3-3']) ? [5, 6, 0] : [0];

        return workdays($date->year, $date->month, $igonore);
    }

    /**
     * Получаем время работы.
     *
     * @delegate
     * @return array
     */
    public function schedule(): array
    {
        return $this->workTime();
    }

    /**
     * Получаем время работы.
     *
     * @return array
     */
    public function workTime(): array
    {
        return [
            'workStartTime' => $this->start_time,
            'workEndTime'   => $this->end_time,
        ];
    }

    /**
     * Получаем количество рабочих дней в неделе.
     *
     * @return array
     */
    public function chartWorkDays(): array
    {
        $workDays = $this->name;

        return match ($workDays) {
            "1-1", "2-2", "3-3" => [5, 6, 0],
            "5-2"   => [6, 0],
            "6-1"   => [0],
            default => throw new InvalidArgumentException("Invalid chart type"),
        };
    }
    /**
     * Получаем время работы.
     *
     * @return array
     */
    public static function getWorkTime(?self $chart): array
    {
        return $chart ? $chart->workTime() : self::defaultWorkTime();
    }

    /**
     * @param WorkChartModel|null $chart
     * @return array
     */
    public static function getWorkDay(?self $chart): array
    {
        return $chart ? $chart->chartWorkDays() : [6, 0];
    }

    /**
     * Получаем default время работы.
     *
     * @return array
     */
    public static function defaultWorkTime(): array
    {
        return [
            'workStartTime' => Timetracking::DEFAULT_WORK_START_TIME,
            'workEndTime'   => Timetracking::DEFAULT_WORK_END_TIME,
        ];
    }

    public function workChartType(): BelongsTo
    {
        return $this->belongsTo(WorkChartTypeRb::class, 'work_charts_type', 'id');
    }
}
