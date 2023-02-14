<?php

namespace App\Models\Tariff;

use App\Enums\Payments\PaymentStatusEnum;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class TariffPayment extends Model
{
    protected $table = 'tariff_payment';

    public $timestamps = true;

    protected $casts = [
        'created_at'  => 'date:d.m.Y H:i',
        'updated_at'  => 'date:d.m.Y H:i',
        'expire_date'  => 'date:d.m.Y',
    ];

    protected $fillable = [
        'owner_id',
        'tariff_id',
        'extra_user_limit',
        'expire_date',
        'auto_payment',
        'payment_id',
        'service_for_payment',
        'status'
    ];

    /**
     * @return HasOne
     */
    public function tariff(): HasOne
    {
        return $this->hasOne(Tariff::class, 'id', 'tariff_id');
    }

    /**
     * @return HasOne
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    /**
     * Return the tariff payment info with tariff for particular owner.
     *
     * @param int $ownerId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getTarriffPaymentsByOwnerId(int $ownerId)
    {
        return $this->with('owner')
            ->with('tariff')
            ->where('owner_id', $ownerId)
            ->get();
    }

    /**
     * Returns valid tarif for current subdomain.
     *
     * @return \Illuminate\Database\Eloquent\Builder|Model|object
     */
    public function getValidTarriffPayments()
    {
        $today = Carbon::today();

        return $this->select(
            'tariff_payment.id',
            'tariff_payment.owner_id',
            'tariff_payment.tariff_id',
            'tariff_payment.extra_user_limit',
            'tariff_payment.expire_date',
            'tariff_payment.expire_date',
            'tariff_payment.created_at',
            'tariff.kind',
            'tariff.validity',
            'tariff.users_limit',
            \DB::raw('(`tariff`.`users_limit` + `tariff_payment`.`extra_user_limit`) as total_user_limit')
        )
            ->leftJoin('tariff', 'tariff.id', 'tariff_payment.tariff_id')
            ->where('tariff_payment.expire_date', '>', $today)
            ->orderBy('tariff_payment.expire_date', 'desc')
            ->groupBy('tariff_payment.id')
            ->first();
    }

    /**
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param string $expireDate
     * @param string $paymentId
     * @param string $serviceForPayment
     * @param bool $autoPayment
     * @return object
     * @throws Exception
     */
    public static function createPaymentOrFail(
        int $tariffId,
        int $extraUsersLimit,
        string $expireDate,
        string $paymentId,
        string $serviceForPayment,
        bool $autoPayment = false
    ): object
    {
        try {
            return self::query()->create([
                'owner_id'          => auth()->id() ?? 5,
                'tariff_id'         => $tariffId,
                'extra_user_limit'  => $extraUsersLimit,
                'expire_date'       => $expireDate,
                'auto_payment'      => $autoPayment,
                'payment_id'        => $paymentId,
                'status'            => PaymentStatusEnum::STATUS_PENDING,
                'service_for_payment' => $serviceForPayment
            ]);
        } catch (Exception $exception) {
            throw new Exception('При сохранений данных произошла ошибка');
        }
    }
}
