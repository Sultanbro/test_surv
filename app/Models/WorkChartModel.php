<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkChartModel extends Model
{
    use HasFactory;

    protected $table = 'work_charts';

    protected $casts = [
        'day_off' => 'array',
    ];

    protected $fillable = [
        'name',
        'time_beg',
        'time_end',
        'day_off',
    ];

    public static function indexModel()
    {
        return WorkChartModel::all();
    }

    public static function createModel($attribute)
    {
        return WorkChartModel::firstOrCreate([
            'name' => $attribute['name'],
            'time_beg' => $attribute['time_beg'],
            'time_end' => $attribute['time_end']], $attribute);
    }

    public static function showModel($id):object
    {
        return WorkChartModel::findOrFail($id);
    }

    public static function updateModel($attribute, $id):object
    {
        $model = WorkChartModel::findOrFail($id);
        $model->update($attribute);
        return $model;
    }

    public static function deleteModel($id)
    {
        return WorkChartModel::findOrFail($id)->destroy($id);
    }
}
