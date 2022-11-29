<?php

namespace App\Models\Award;

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
        'format',
        'path',
        'styles',
        'award_category_id',
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
    public function category(): BelongsTo
    {
        return $this->belongsTo(AwardCategory::class, 'award_category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'award_user', 'award_id','user_id')
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
