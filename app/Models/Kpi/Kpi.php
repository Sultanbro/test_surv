<?php

namespace App\Models\Kpi;

use App\Models\History;
use App\Models\Kpi\Traits\Expandable;
use App\Models\Kpi\Traits\Targetable;
use App\Models\Kpi\Traits\WithActivityFields;
use App\Models\Kpi\Traits\WithCreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kpi extends Model
{
    use HasFactory, SoftDeletes, Targetable, WithCreatorAndUpdater, WithActivityFields, Expandable;

    protected $table = 'kpis';

    protected $appends = ['target', 'expanded'];
    
    protected $fillable = [
        'targetable_id',
        'targetable_type',
        'completed_80',
        'completed_100',
        'lower_limit',
        'upper_limit',
        'colors',
        'created_by',
        'updated_by',
        'children',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'created_at'  => 'date:d.m.Y H:i',
        'updated_at'  => 'date:d.m.Y H:i',
        'children'    => 'array',
    ];

    /**
     * One To Many отношения с kpi_items.
     * У одного kpi могут быть несколько показателей.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany('App\Models\Kpi\KpiItem');
    }

    /**
     * История
     * 
     * @return MorphMany
     */
    public function histories()
    {
        return $this->morphMany(History::class, 'historable', 'reference_table', 'reference_id')
            ->orderBy('created_at', 'desc');
    }
}
