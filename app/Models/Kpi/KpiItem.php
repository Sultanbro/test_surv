<?php

namespace App\Models\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiItem extends Model
{
    use HasFactory;

    protected $table = 'kpi_items';

    protected $fillable = [
        'name',
        'activity_id',
        'kpi_id',
        'plan',
        'share'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
