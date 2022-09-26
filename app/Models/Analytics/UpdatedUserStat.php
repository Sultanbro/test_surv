<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UpdatedUserStat extends Model
{
    protected $table = 'updated_user_stats';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'date',
        'activity_id',
        'kpi_item_id',
        'value',
    ];
 
}
