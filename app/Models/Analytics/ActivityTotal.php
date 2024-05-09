<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class ActivityTotal extends Model
{
    protected $table = 'activity_totals';

    public $timestamps = true;

    protected $fillable = [
        'date',
        'user_id',
        'employee_id',
        'total',
        'activity_id'
    ];
}
