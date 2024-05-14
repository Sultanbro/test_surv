<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class IndividualKpi extends Model
{
    public $timestamps = true;

    protected $table = 'kpi_individual';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'kpi_80_99',
        'kpi_100',
        'nijn_porok',
        'verh_porok',
    ];
}
