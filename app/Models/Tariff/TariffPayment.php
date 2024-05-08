<?php

namespace App\Models\Tariff;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Models\CentralUser;
use App\Models\Tenant;
use App\User;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $tenant_id
 * @property int $tariff_id
 * @property int $extra_user_limit
 * @property string $expire_date
 * @property bool $auto_payment
 * @property string $payment_id
 * @property string $service_for_payment
 * @property string $status,
 * @property string $lead_id
 */
class TariffPayment extends Model
{
    protected $connection = 'mysql';

    protected $table = 'tariff_payment';

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'date:d.m.Y H:i',
        'updated_at' => 'date:d.m.Y H:i',
        'expire_date' => 'date:d.m.Y',
    ];

    protected $fillable = [
        'tenant_id',
        'tariff_id',
        'extra_user_limit',
        'expire_date',
        'auto_payment',
        'payment_id',
        'service_for_payment',
        'status',
        'lead_id'
    ];

    public static function getStatus($paymentId): string
    {
        /**  @var TariffPayment $payment */
        $payment = self::query()->where('payment_id', $paymentId)->first();
        return $payment->status;
    }

    /**
     * @return BelongsTo
     */
    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariff_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Returns valid tariff for current subdomain.
     *
     * @return TariffPayment|null
     */
    public static function getValidTariffPayments(): ?TariffPayment
    {
        $today = Carbon::today();

        /** @var TariffPayment */
        return self::query()
            ->select(
                'tariff_payment.id',
                'tariff_payment.tenant_id',
                'tariff_payment.tariff_id',
                'tariff_payment.extra_user_limit',
                'tariff_payment.expire_date',
                'tariff_payment.created_at',
                'tariff_payment.payment_id',
                'tariff.kind',
                'tariff.validity',
                'tariff.users_limit',
                DB::raw('(`tariff`.`users_limit` + `tariff_payment`.`extra_user_limit`) as total_user_limit')
            )
            ->leftJoin('tariff', 'tariff.id', 'tariff_payment.tariff_id')
            ->where('tariff_payment.expire_date', '>', $today->format('Y-m-d'))
            ->where('status', PaymentStatusEnum::STATUS_SUCCESS)
            ->orderBy('tariff_payment.expire_date', 'desc')
            ->groupBy('tariff_payment.id')
            ->first();
    }


    /**
     * Returns bool active payment exists.
     *
     * @param Tenant $tenant
     * @return ?TariffPayment
     */
    public static function getActivePaymentIfExist(Tenant $tenant): ?TariffPayment
    {
        $today = Carbon::today();

        /** @var TariffPayment */
        return $tenant
            ->tariffPayment()
            ->where('expire_date', '>', $today)
            ->where(function ($query) {
                $query->where('status', PaymentStatusEnum::STATUS_SUCCESS);
            })
            ->first();
    }

    /**
     * Return the last tariff payment with status pending
     *
     * @param string $tenantId
     * @return TariffPayment
     */
    public static function getLastPendingTariffPayment(string $tenantId): TariffPayment
    {
        /** @var TariffPayment */
        return self::getBasePendingTariffsQuery($tenantId)
            ->latest()
            ->first();
    }

    /**
     * Return the base qb for tariff payments with status pending
     *
     * @param string|null $tenantId
     * @return QueryBuilder
     */
    public static function getBasePendingTariffsQuery(?string $tenantId): QueryBuilder
    {
        return self::query()
            ->when($tenantId, fn($query) => $query->where('tenant_id', $tenantId))
            ->where('status', PaymentStatusEnum::STATUS_PENDING);
    }

    /**
     * @param string $tenantId
     * @param int $tariffId
     * @param int $extraUsersLimit
     * @param string $expireDate
     * @param string $paymentId
     * @param string $serviceForPayment
     * @param bool $autoPayment
     * @return TariffPayment
     * @throws Exception
     */
    public static function createPaymentOrFail(
        string $tenantId,
        int    $tariffId,
        int    $extraUsersLimit,
        string $expireDate,
        string $paymentId,
        string $serviceForPayment,
        bool   $autoPayment = false
    ): TariffPayment
    {

//        try {
        /** @var TariffPayment */
        return self::query()->create([
            'tenant_id' => $tenantId,
            'tariff_id' => $tariffId,
            'extra_user_limit' => $extraUsersLimit,
            'expire_date' => $expireDate,
            'auto_payment' => $autoPayment,
            'payment_id' => $paymentId,
            'status' => PaymentStatusEnum::STATUS_PENDING,
            'service_for_payment' => $serviceForPayment
        ]);
//        } catch (Exception) {
//            throw new Exception('При сохранений данных произошла ошибка');
//        }
    }

    public function updateStatusToSuccess(): void
    {
        $this->update([
            'status' => PaymentStatusEnum::STATUS_SUCCESS
        ]);
    }

    /**
     * @throws Exception
     */
    public static function savePayment(NewTariffPaymentDTO $dto, PaymentToken $token): static
    {
        $tariff = Tariff::getTariffById($dto->tariffId);
        return self::createPaymentOrFail(
            tenant('id'),
            $dto->tariffId,
            $dto->extraUsersLimit,
            $tariff->calculateExpireDate(),
            $token->token,
            $dto->provider
        );
    }
}
