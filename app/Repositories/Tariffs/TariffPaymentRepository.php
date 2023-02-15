<?php

namespace App\Repositories\Tariffs;

use App\Repositories\CoreRepository;
use App\Models\Tariff\TariffPayment as Model;

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
    protected function getModelClass()
    {
        return Model::class;
    }

    /**
     * @param int $ownerId
     * @return object|null
     */
    public function getTariffByPayment(
        int $ownerId
    ): ?object
    {
        return $this->model()->with('tariff')
            ->orderBy('expire_date', 'desc')
            ->where([
            ['status', '=', 'succeeded'],
            ['expire_date', '<', now()],
            ['owner_id', '=', $ownerId]
        ])->first();
    }
}