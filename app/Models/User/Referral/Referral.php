<?php

namespace App\Models\User\Referral;

use App\Service\Referral\ReferralInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model implements ReferralInterface
{
    use HasFactory, SoftDeletes;

    protected $table = 'referrals';

    protected $fillable = [
          'referrer_id'
        , 'token'
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(
            Referrer::class
            , 'referrer_id'
            , 'id'
        );
    }

    public function url(): string
    {
        return config('services.referral_url') . '/' . $this->token;
    }
}
