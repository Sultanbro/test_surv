<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QualityParamValue extends Model
{
    public $timestamps = false;

    protected $table = 'quality_param_values';

    protected $fillable = [
        'param_id',
        'record_id',
        'value',
    ];

    
}
