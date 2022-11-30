<?php

namespace App\Models\Award;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AwardCategory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'hide',
        'type',
        'created_by'
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
        return $this->belongsTo(AwardType::class, 'type');
    }

    /**
     * Одна категория хранит множество наград
     * @return HasMany
     */
    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }

    public function creator(){
        return $this->belongsTo(\App\User::class, 'created_by');
    }


}
