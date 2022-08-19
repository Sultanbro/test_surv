<?php

namespace App\Models\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kpi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpis';

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
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['target', 'expanded'];

    /**
     * models
     */
    const TARGETS = [
        'App\User' => 1,
        'App\ProfileGroup' => 2,
        'App\Position' => 3,
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
     * Get the parent targetable model (user, group, position).
     */
    public function targetable()
    {
        return $this->morphTo();
    }

    /**
     * Таргет
     * @return array | null
     */
    public function getTargetAttribute() 
    {
        $target = $this->targetable;
        return $target ? [
            'id' => $this->targetable_id,
            'name' => $target->name,
            'type' => self::TARGETS[$this->targetable_type],
        ] : null;
    }

    /**
     * Helper for vue
     * @return array | null
     */
    public function getExpandedAttribute() 
    {
        return false;
    }
}
