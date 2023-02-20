<?php

namespace App\Models\WorkChart;

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
}
