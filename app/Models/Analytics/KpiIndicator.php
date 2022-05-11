<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class KpiIndicator extends Model
{
    protected $table = 'kpi_indicators';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'group_id',
        'activity_id',
        'plan',
        'ud_ves',
        'plan_unit',// метод расчета
        'unit', // ед изм
    ];
}
