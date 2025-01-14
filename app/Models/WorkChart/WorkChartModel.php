<?php

namespace App\Models\WorkChart;

use App\DTO\WorkChart\StoreWorkChartDTO;
use App\DTO\WorkChart\UpdateWorkChartDTO;
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
        'work_charts_type',
        'workdays',
        'rest_time',
        'floating_dayoffs'
    ];

    const WORK_CHART_TYPE_USUAL = 1;
    const WORK_CHART_TYPE_REPLACEABLE = 2;
    const MAX_CHART_DAYS_REPLACEABLE = 30;
    const WORK_DAYS_PER_MONTH_DEFAULT_REPLACEABLE = 15;
    const WEEK_WITHOUT_DAYOFFS = 127;
    const DAYS_IN_WEEK = 7;

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
            'workRestTime'   => $this->rest_time,
            'workChartsType' => $this->work_charts_type,
        ];
    }

    /**
     * Получаем количество рабочих дней в неделе.
     *
     * @return array
     */
    public function chartWorkDays(): array
    {
        $floatingDayoffs = $this->floating_dayoffs ?? null;
        if ($floatingDayoffs){
            $workDays = (string)WorkChartModel::DAYS_IN_WEEK - $floatingDayoffs."-".$floatingDayoffs;
        }else{
            $workDays = $this->name;
        }

        return match ($workDays) {
            "6-1" => [0],
            "5-2" => [6,0],
            "4-3" => [5,6,0],
            "3-4" => [4,5,6,0],
            "2-5" => [3,4,5,6,0],
            "1-6" => [2,3,4,5,6,0],
            "1-1", "2-2", "3-3" => [5,6,0],
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
            'workRestTime'   => 0,
            'workChartsType' => Timetracking::DEFAULT_WORK_CHARTS_TYPE,
        ];
    }

    /**
     * Получает название типы смен
     * @return BelongsTo
     */
    public function workChartType(): BelongsTo
    {
        return $this->belongsTo(WorkChartTypeRb::class, 'work_charts_type', 'id');
    }

    /**
     * Проверяем смену на дубликат.
     * @param StoreWorkChartDTO|UpdateWorkChartDTO $dto
     * @return bool
     */
    public static function checkDuplicate(StoreWorkChartDTO | UpdateWorkChartDTO $dto): bool {
        $data = $dto->toArray();
        return self::where('name', $data['name'])
            ->where('start_time', $data['start_time'])
            ->where('end_time', $data['end_time'])
            ->where('text_name', $data['text_name'])
            ->where('work_charts_type', $data['work_charts_type'])
            ->where('workdays', $data['workdays'])
            ->where('rest_time', $data['rest_time'])
            ->where('floating_dayoffs', $data['floating_dayoffs'])
            ->exists();
    }

    public static function convertWorkDays(int $workdays): array
    {
        $hexDay = strrev(decbin($workdays));
        $days = str_pad($hexDay, 7, "0", STR_PAD_RIGHT);

        $offDays = [];
        for ($i = 0; $i < strlen($days); $i++) {
            if ( $days[$i] === '0'){
                $offDays[] = $i === 6 ? 0 : $i+1;
            }
        }
        return $offDays;
    }
}
