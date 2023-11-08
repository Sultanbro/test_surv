<?php

namespace App\Models\User\Referral;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @author Vahagn
 * */
trait Referrer
{
    public function referrals(): hasMany
    {
        return $this->hasMany(
            __CLASS__
            , 'referrer_id'
            , 'id'
        );
    }

    public function appliedReferrals(): hasMany
    {
        return $this->hasMany(
            __CLASS__
            , 'referrer_id'
            , 'id'
        )
            ->withTrashed()
            ->whereRelation('description', 'is_trainee', 0);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(
            __CLASS__
            , 'referrer_id'
            , 'id'
        );
    }

    public function hasParent(): bool
    {
        return !!$this->referrer_id;
    }

    public function url(): string
    {
        return "https://job.bpartners.kz/ref?r=$this->id";
    }
}
