<?php

namespace App\Repositories\Tariffs;

use App\Models\Tariff\TariffSubscription;
use App\Repositories\CoreRepository;
use App\Models\Tariff\TariffSubscription as Model;

/**
 * Класс для работы с Repository.
 */
class TariffPaymentRepository extends CoreRepository
{
    /**
     * Здесь используется модель для работы с Repository {{ App\Models\{name} }}
     *
     * @return string
     */
    protected function getModelClass(): string
    {
        return Model::class;
    }

    /**
     * @return bool
     */
    public function tariffForOwnerAlreadyExist(): bool
    {
        return $this->model()
            ->query()
            ->where('tenant_id', tenant('id'))
            ->where('expire_date', '<', now()->format('Y-m-d'))
            ->where('status', 'succeeded')
            ->exists();
    }

    /**
     * @param int $ownerId
     * @return object|null
     */
    public function getTariffByPayment(
        int $ownerId
    ): ?TariffSubscription
    {
        /** @var TariffSubscription */
        return $this->model()
            ->query()
            ->where('tenant_id', tenant('id'))
            ->where('expire_date', '<', now()->format('Y-m-d'))
            ->where('status', 'succeeded')
            ->first();
    }
}