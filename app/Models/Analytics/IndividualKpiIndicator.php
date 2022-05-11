<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class IndividualKpiIndicator extends Model
{
    protected $table = 'kpi_indicators_individual';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'user_id',
        'group_id',
        'activity_id',
        'plan',
        'ud_ves',
        'plan_unit',// метод расчета
        'unit', // ед изм
    ];
}
