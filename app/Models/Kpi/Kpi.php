<?php

namespace App\Models\Kpi;

use App\Models\History;
use App\Models\Kpi\Traits\Expandable;
use App\Models\Kpi\Traits\Targetable;
use App\Models\Kpi\Traits\WithActivityFields;
use App\Models\Kpi\Traits\WithCreatorAndUpdater;
use App\Position;
use App\ProfileGroup;
use App\Traits\ActivateAbleModelTrait;
use App\Traits\TargetJoin;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kpi extends Model
{
    use HasFactory, SoftDeletes, Targetable, WithCreatorAndUpdater, WithActivityFields, Expandable, ActivateAbleModelTrait, TargetJoin;

    public const MORHPS = [
        User::class => 'users',
        ProfileGroup::class => 'profile_groups',
        Position::class => 'positions',
    ];
    public $timestamps = true;
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
        'is_active',
        'read_by',
        'off_limit',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'created_at' => 'date:d.m.Y H:i',
        'updated_at' => 'date:d.m.Y H:i',
        'children' => 'array',
        'read_by' => 'array',
    ];

    public function kpiable(): MorphTo
    {
        return $this->morphTo(
            'kpiable',
            'targetable_type',
            'targetable_id'
        );
    }

    public function kpiables(): Collection
    {
        $users = $this->users()->get();
        $positions = $this->positions()->get();
        $groups = $this->groups()->get();
        return $groups->merge($positions)
            ->merge($users);
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'kpiable',
            'kpiables'
        );
    }

    public function positions(): MorphToMany
    {
        return $this->morphedByMany(
            Position::class,
            'kpiable',
            'kpiables'
        );
    }

    /**---------------------------------------------**/
    public function groups(): MorphToMany
    {
        return $this->morphedByMany(
            ProfileGroup::class,
            'kpiable',
            'kpiables'
        );
    }
    /**---------------------------------------------**/

    /**
     * One To Many отношения с users.
     * У одного kpi может быть 1 пользователь.
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'targetable_id')
            ->withTrashed();
    }

    /**
     * One To Many отношения с kpi_items.
     * У одного kpi могут быть несколько показателей.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany('App\Models\Kpi\KpiItem')
            ->withTrashed();
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

    public function histories_latest()
    {
        return $this->morphOne(History::class, 'historable', 'reference_table', 'reference_id')
            ->orderBy('created_at', 'desc')->latest();
    }

    public function saveTarget(array $kpiable): void
    {
        if ($kpiable['kpiable_type'] === User::class) $this->users()->attach($kpiable['kpiable_id']);
        if ($kpiable['kpiable_type'] === ProfileGroup::class) $this->groups()->attach($kpiable['kpiable_id']);
        if ($kpiable['kpiable_type'] === Position::class) $this->positions()->attach($kpiable['kpiable_id']);
    }
}
