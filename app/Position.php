<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Permission\Traits\HasRoles;

class Position extends Model
{
    use HasRoles;
    
    public $timestamps = true;

    protected $guard_name = 'web';

    protected $table = 'position';

    protected $fillable = [
        'book_groups',
        'position',
        'indexation', // Ведется ли индексация в течение одного года
        'sum', // Сумма
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
    public function descriptions(): HasMany
    {
        return $this->hasMany(PositionDescription::class, 'position_id');
    }
}
