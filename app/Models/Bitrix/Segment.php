<?php

namespace App\Models\Bitrix;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $table = 'bitrix_segments';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'on_lead', // Id сегмента в лиде
        'on_deal', // Id сегмента в сделке
        'active',
    ];
}
