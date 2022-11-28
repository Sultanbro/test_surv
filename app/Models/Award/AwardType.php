<?php

namespace App\Models\Award;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AwardType extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'key',
        'description'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasMany
     */
    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }
}
