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
}
