<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QualityRecordMonthlyStat extends Model
{
    public $timestamps = false;

    protected $table = 'quality_record_monthly_stats';

    protected $fillable = [
        'month',
        'year',
        'total',
        'group_id',
        'user_id',
    ];
}
