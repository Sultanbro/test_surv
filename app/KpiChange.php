<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\Analytics\Activity;
use Carbon\Carbon;
use App\User;

class KpiChange extends Model
{
    public $timestamps = true;

    protected $table = 'kpi_changes';

    protected $fillable = [
        'kpi_id',
        'user_id',
        'group_id',
        'kpi_80_99',
        'kpi_100',
        'nijn_porok',
        'verh_porok',
        'month',
        'year',
    ];
}
