<?php

namespace App\Models;

use App\Enums\Tariff\TariffKindEnum;
use App\Models\Portal\Portal;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\MaintenanceMode;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory, MaintenanceMode;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'global_id'
        ];
    }

    /**
     * @param string $id
     * @return Builder
     */
    public static function getById(string $id): Builder
    {
        return self::query()->where('id', $id);
    }

    /**
     * @return HasOne
     */
    public function portal(): HasOne //TODO Portal refactor: replace HasOne with HasMany
    {
        return $this->hasOne(Portal::class, 'tenant_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            CentralUser::class,
            'tenant_pivot',
            'tenant_id',
            'user_id'
        );
    }

    public function owner(): BelongsToMany
    {
        return $this->belongsToMany(
            CentralUser::class,
            'tenant_pivot',
            'tenant_id',
            'user_id'
        )->where('owner', 1);
    }

    /**
     * @return HasOne
     */
    public function tariffPayment(): HasOne
    {
        return $this->hasOne(TariffSubscription::class, 'tenant_id', 'id');
    }

    public function hasActiveTariff(): bool
    {
        return $this->tariffPayment()
            ->join('tariffs', 'tariff_payment.tariff_id', '=', 'tariffs.id')
            ->where('tariffs.kind', '!=', TariffKindEnum::Free)
            ->whereDate('subscriptions.expire_date', '>=', now())
            ->exists();
    }
}