<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QualityRecord extends Model
{
    public $timestamps = true;

    protected $table = 'quality_records';

    protected $fillable = [
        'employee_id',
        'segment_id',
        'phone',
        'interlocutor',
        'day_of_delay',
        'listened_on',
        'params',
        'total',
        'comments',
        'user_id',
        'group_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'employee_id', 'id')->withTrashed();
    }

    public function param_values()
    {
        return $this->hasMany('App\QualityParamValue', 'record_id', 'id');
    }


     
}
