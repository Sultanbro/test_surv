<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingDay extends Model
{
    public $timestamps = true;

    protected $table = 'working_days';

    protected $fillable = [
        'name'
    ];
}
