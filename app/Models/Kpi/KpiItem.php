<?php

namespace App\Models\Kpi;

use App\Models\History;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'daily_plan',
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

    public function histories(): MorphMany
    {
        return $this->morphMany(History::class, 'historable', 'reference_table', 'reference_id')
            ->orderBy('created_at', 'desc');
    }

    public function histories_latest()
    {
        return $this->morphOne(History::class, 'historable', 'reference_table', 'reference_id')
            ->orderBy('created_at', 'desc')->latest();
    }

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class, 'kpi_id');
    }
}
