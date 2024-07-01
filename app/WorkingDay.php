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

    /**
     * ID = 1;
     * График 5-2;
     */
    const FIVE_DAYS = 5;

    /**
     * ID = 2;
     * График 6-1;
     */
    const SIX_DAYS = 6;
}
