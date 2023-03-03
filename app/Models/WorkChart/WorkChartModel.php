<?php

namespace App\Models\WorkChart;

use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkChartModel extends Model
{
    use HasFactory;

    protected $table = 'work_charts';

    protected $fillable = [
        'name',
        'start_time',
        'end_time'
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
     * @param string $name
     * @param string $startTime
     * @param string $endTime
     * @return Model
     */
    public static function createModel(
        string $name,
        string $startTime,
        string $endTime
    ): Model
    {
        return WorkChartModel::query()->create([
            'name' => $name,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);
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
}
