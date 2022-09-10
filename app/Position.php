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

    const OPERATOR_ID = 32;
    const INTERN_ID = 47;

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
}
