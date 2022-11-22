<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'award_type_id',
        'format',
        'icon',
        'path',
        'styles',
        'hide',
        'name',
        'description',
        'targetable_type',
        'targetable_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return BelongsTo
     */
    public function awardType(): BelongsTo
    {
        return $this->belongsTo(AwardType::class, 'award_type_id');
    }
    /**
     * Одна награда может принадлежать нескольким курсам
     * @return HasMany
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Award::class);
    }
    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Award::class, 'award_user', 'award_id','user_id')
            ->withTimestamps();

    }

    public function getPathAttribute($value){
        if ($value != ''){
            $disk = \Storage::disk('s3');

            return $disk->temporaryUrl(
                $value, now()->addMinutes(360)
            );
        }
        return $value;

    }
}
