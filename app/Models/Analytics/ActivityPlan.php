<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class ActivityPlan extends Model
{
    protected $table = 'activity_plans';

    public $timestamps = true;

    protected $fillable = [
        'activity_id',
        'month',
        'year',
        'plan',
        'ud_ves',
        'plan_unit',
    ];
    

}
