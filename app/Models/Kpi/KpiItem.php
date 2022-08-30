<?php

namespace App\Models\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi_items';

    protected $fillable = [
        'name',
        'activity_id',
        'kpi_id',
        'plan',
        'share',
        'method',
        'unit',
        'cell',
        'common',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * 
     */
    public function activity()
    {
        return $this->belongsTo( 'App\Models\Analytics\Activity', 'activity_id', 'id')
            ->withTrashed();
    }
}
