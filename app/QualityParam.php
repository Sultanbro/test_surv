<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QualityParam extends Model
{
    public $timestamps = true;

    protected $table = 'quality_params';

    protected $fillable = [
        'name',
        'group_id',
        'active',
    ];



}
