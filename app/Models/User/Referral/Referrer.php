<?php

namespace App\Models\User\Referral;

use App\Service\Referral\Core\ReferrerInterface;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $user_id
 * @property-read User $user
 */
class Referrer extends Model implements ReferrerInterface
{
    use HasFactory, SoftDeletes;

    protected $table = 'referrers';

    protected $fillable = [
          'user_id'
        , 'parent_referrer_id' // this link to the same table
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
            , 'user_id'
            , 'id'
        );
    }

    public function referral(): HasOne
    {
        return $this->hasOne(
            Referral::class
            , 'referrer_id'
            , 'id'
        );
    }

    public function parentReferrer(): BelongsTo
    {
        return $this->belongsTo(
            __CLASS__
            , 'parent_referrer_id'
            , 'id'
        );
    }

    public function referees(): HasMany
    {
        return $this->hasMany(
            __CLASS__
            , 'parent_referrer_id'
            , 'id'
        );
    }

    public function hasParentReferrer(): bool
    {
        return !!$this->parent_referrer_id;
    }
}
