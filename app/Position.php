<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $book_groups
 * @property string $position
 * @property string $indexation
 * @property float $sum
 * @property bool $is_head
 * @property bool $is_spec
 * @property string $ckp_status
 * @property string $ckp
 * @property string $ckp_link
 * */
class Position extends Model
{
    use HasRoles, SoftDeletes, HasFactory;

    public $timestamps = true;

    protected $guard_name = 'web';

    protected $table = 'position';

    protected $fillable = [
        'book_groups',
        'position',
        'indexation', // Ведется ли индексация в течение одного года
        'sum', // Сумма
        'is_head', // boolean
        'is_spec', // boolean
        'ckp_status',
        'ckp',
        'ckp_link'
    ];

    /**
     * ID оператора по должности
     */
    const OPERATOR_ID = 32;
    /**
     * ID стажера по должности
     */
    const INTERN_ID = 47;

    /**
     * ID старшего рекрутера по должности
     */
    const HEAD_RECRUITER_ID = 46;

    /**
     * ID рекрутера по должности
     */
    const RECRUITER_ID = 48;

    /**
     * ID Руководитель группы.
     */
    const GROUP_HEAD = 45;

    public static function operator(): Position
    {
        /** @var Position */
        return self::query()
            ->where('name', 'Оператор контакт-центра')
            ->firstOrCreate();
    }

    /**
     * @return MorphMany
     */
    public function qpremium(): MorphMany
    {
        return $this->morphMany('App\Models\QuartalPremium', 'targetable', 'targetable_type', 'targetable_id');
    }

    public function kpisMany(): MorphToMany
    {
        return $this->morphToMany(
            'App\Models\Kpi\Kpi',
            'kpiable',
            'kpiables',
            'kpiable_id',
            'kpi_id',
            'id',
            'id'
        );
    }

    /**
     * @return MorphMany
     */
    public function bonuses(): MorphMany
    {
        return $this->morphMany('App\Models\Kpi\Bonus', 'targetable', 'targetable_type', 'targetable_id');
    }

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany('App\User', 'position_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function activeUsers(): HasMany
    {
        return $this->hasMany(User::class)
            ->whereNull('users.deleted_at');
    }

    /**
     * @return HasMany
     */
    public function descriptions(): HasMany
    {
        return $this->hasMany(PositionDescription::class, 'position_id');
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     */
    public static function getById(
        int $id
    ): Model|Collection|Builder|array|null
    {
        return self::query()->findOrFail($id);
    }
}
