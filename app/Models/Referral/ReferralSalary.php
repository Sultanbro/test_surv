<?php

namespace App\Models\Referral;

use App\Service\Referral\Core\PaidType;
use App\User;
use Carbon\Carbon;
use Database\Factories\Referral\ReferralSalaryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $referral_id
 * @property int $referrer_id
 * @property float $amount
 * @property bool $is_paid
 * @property string $comment
 * @property Carbon $date
 * @property PaidType $type
 */
class ReferralSalary extends Model
{
    use HasFactory;

    protected $table = 'referral_salaries';

    protected $fillable = [
        'referral_id',
        'referrer_id',
        'amount',
        'is_paid',
        'comment',
        'type',
        'date',
    ];

    protected $casts = [
        'type' => PaidType::class,
        'is_paid' => "boolean",
    ];

    protected static function newFactory(): ReferralSalaryFactory
    {
        return new ReferralSalaryFactory();
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'referral_id',
            'id',
        );
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'referrer_id',
            'id',
        );
    }
}
