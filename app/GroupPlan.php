<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupPlan extends Model
{
    protected $table = 'group_plan';

    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'calls_per_day',
        'minutes_per_day',
        'consent_per_day'
    ];

    public function group()
    {
        return $this->belongsTo('App\ProfileGroup');
    }
}
