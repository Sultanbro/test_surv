<?php

namespace App\Models\Tariff;

use App\DTO\Payment\NewSubscriptionDTO;
use App\Enums\Payments\PaymentStatusEnum;
use App\Facade\Payment\Gateway;
use App\Models\Tenant;
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
 * @property string $payment_provider
 * @property string $status
 * @property string $lead_id
 */
class TariffSubscription extends Model
{
    protected $connection = 'mysql';

    protected $table = 'tariff_subscriptions';

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'date:d.m.Y H:i',
        'updated_at' => 'date:d.m.Y H:i',
        'expire_date' => 'date:d.m.Y'
    ];

    protected $fillable = [
        'tenant_id',
        'tariff_id',
        'extra_user_limit',
        'expire_date',
        'auto_payment',
        'payment_id',
        'payment_provider',
        'status',
        'lead_id'
    ];

    public static function getStatus($paymentId): string
    {
        /**  @var TariffSubscription $payment */
        $payment = self::query()->where('payment_id', $paymentId)->first();
        return $payment->status;
    }

    public static function hasTrial(mixed $tenant): bool
    {
        return self::query()
            ->where('tenant_id', $tenant)
            ->where('payment_id', 'trial')
            ->exists();
    }

    public static function getByTransactionId(int|string $getTransactionId): ?TariffSubscription
    {
        /** @var TariffSubscription */
        return self::query()
            ->where('payment_id', $getTransactionId)
            ->first();
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
     * @param string|null $tenant
     * @return TariffSubscription|null
     */
    public static function getValidTariffPayment(string $tenant = null): ?TariffSubscription
    {
        $today = Carbon::today();

        /** @var TariffSubscription */
        return self::query()
            ->when($tenant, fn($query) => $query->where('tenant_id', $tenant))
            ->select(
                'tariff_subscriptions.id as subscription_id',
                'tariff_subscriptions.tenant_id',
                'tariff_subscriptions.tariff_id',
                'tariff_subscriptions.extra_user_limit',
                'tariff_subscriptions.expire_date',
                'tariff_subscriptions.created_at',
                'tariff_subscriptions.payment_id',
                'tariff_subscriptions.payment_provider',
                'tariff.kind',
                'tariff.validity',
                'tariff.users_limit',
                DB::raw('(`tariff`.`users_limit` + `tariff_subscriptions`.`extra_user_limit`) as total_user_limit')
            )
            ->leftJoin('tariff', 'tariff.id', 'tariff_subscriptions.tariff_id')
            ->where('tariff_subscriptions.expire_date', '>', $today->format('Y-m-d'))
            ->where('status', PaymentStatusEnum::STATUS_SUCCESS)
            ->orderBy('tariff_subscriptions.expire_date', 'desc')
            ->groupBy('tariff_subscriptions.id')
            ->first();
    }
    public static function hasValidTariffPayment(string $tenant = null): bool
    {
        $today = Carbon::today();

        return self::query()
            ->when($tenant, fn($query) => $query->where('tenant_id', $tenant))
            ->select(
                'tariff_subscriptions.id',
                'tariff_subscriptions.tenant_id',
                'tariff_subscriptions.tariff_id',
                'tariff_subscriptions.extra_user_limit',
                'tariff_subscriptions.expire_date',
                'tariff_subscriptions.created_at',
                'tariff_subscriptions.payment_id',
                'tariff_subscriptions.payment_provider',
                'tariff.kind',
                'tariff.validity',
                'tariff.users_limit',
                DB::raw('(`tariff`.`users_limit` + `tariff_subscriptions`.`extra_user_limit`) as total_user_limit')
            )
            ->leftJoin('tariff', 'tariff.id', 'tariff_subscriptions.tariff_id')
            ->where('tariff_subscriptions.expire_date', '>', $today->format('Y-m-d'))
            ->where('status', PaymentStatusEnum::STATUS_SUCCESS)
            ->orderBy('tariff_subscriptions.expire_date', 'desc')
            ->groupBy('tariff_subscriptions.id')
            ->exists();
    }


    /**
     * Returns bool active payment exists.
     *
     * @param Tenant $tenant
     * @return ?TariffSubscription
     */
    public static function getActivePaymentIfExist(Tenant $tenant): ?TariffSubscription
    {
        $today = Carbon::today();

        /** @var TariffSubscription */
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
     * @return TariffSubscription
     */
    public static function getLastPendingTariffPayment(string $tenantId): TariffSubscription
    {
        /** @var TariffSubscription */
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
     * @param string $paymentProvider
     * @param bool $autoPayment
     * @return TariffSubscription
     * @throws Exception
     */
    public static function new(
        string $tenantId,
        int    $tariffId,
        int    $extraUsersLimit,
        string $expireDate,
        string $paymentId,
        string $paymentProvider,
        bool   $autoPayment = false
    ): TariffSubscription
    {
        /** @var TariffSubscription */
        return self::query()->create([
            'tenant_id' => $tenantId,
            'tariff_id' => $tariffId,
            'extra_user_limit' => $extraUsersLimit,
            'expire_date' => $expireDate,
            'payment_id' => $paymentId,
            'status' => PaymentStatusEnum::STATUS_PENDING,
            'payment_provider' => $paymentProvider
        ]);
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
    public static function subscribe(NewSubscriptionDTO $dto, Transaction $token): static
    {
        $tariff = Tariff::find($dto->tariffId);

        return self::new(
            $dto->tenantId,
            $dto->tariffId,
            $dto->extraUsersLimit,
            $tariff->calculateExpireDate(),
            $token->id,
            $dto->provider
        );
    }

    public function getCurrency(): string
    {
        return Gateway::provider($this->payment_provider)->currency();
    }
}
