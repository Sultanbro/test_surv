<?php

namespace App\Models;

use App\Models\Kpi\Traits\Expandable;
use App\Models\Kpi\Traits\Targetable;
use App\Models\Kpi\Traits\WithCreatorAndUpdater;
use App\Models\Kpi\Traits\WithActivityFields;
use App\Models\Scopes\ActiveScope;
use App\Traits\ActivateAbleModelTrait;
use App\Traits\TargetJoin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class QuartalPremium extends Model
{
    use HasFactory, SoftDeletes, Targetable, WithCreatorAndUpdater, WithActivityFields, Expandable, ActivateAbleModelTrait, TargetJoin;

    protected $table = 'quartal_premiums';

    protected $appends = ['target', 'group_id', 'source', 'expanded', 'read'];
    
    protected $casts = [
        'created_at'  => 'date:d.m.Y H:i',
        'updated_at'  => 'date:d.m.Y H:i',
        'read_by'     => 'array',
    ];

    protected $fillable = [
        'targetable_id',
        'targetable_type',
        'activity_id',
        'title',
        'text',
        'plan',
        'from',
        'to',
        'sum',
        'created_by',
        'updated_by',
        'is_active',
        'read_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Получает все активные кв-премий без доп запросов.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ActiveScope);
    }

    /**
     * @param int $id
     * @return Model
     */
    public static function getById(
        int $id
    ): Model
    {
        return self::query()->findOrFail($id);
    }

    /**
     * @return Builder
     */
    public static function admin(): Builder
    {
        return self::with('creator')->withoutGlobalScope(ActiveScope::class);
    }

    /**
     * @return BelongsTo
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo( 'App\Models\Analytics\Activity', 'activity_id', 'id')
            ->withTrashed();
    }

    /**
     * Read Accessor
     * @return bool
     */
    public function getReadAttribute()
    {
        $id = Auth::id();

        return $id 
            ? in_array($id, $this->read_by ?? [])
            : false;
    }
}
