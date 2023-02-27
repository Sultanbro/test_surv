<?php
declare(strict_types=1);

namespace App\Service\Admin\Owners;

use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Models\Tenant;
use App\Repositories\Tariffs\TariffPaymentRepository;
use App\Support\Core\CustomException;

/**
* Класс для работы с Service.
*/
class OwnerInfoService
{
    /**
     * @param TariffPaymentRepository $paymentRepository
     */
    public function __construct(
        public TariffPaymentRepository $paymentRepository
    )
    {}

    /**
     * @return array
     */
    public function handle(): array
    {
        $ownerId = auth()->id() ?? 1;
        $owner = CentralUser::getById($ownerId)->first();
        $domain = $owner->tenants()->where('tenant_id', tenant('id'))->exists();

        abort_if(!$domain, ErrorCode::FORBIDDEN, "У вас не доступа для этого суб домена");

        return [
            'owner' => $owner,
            'tariff' => $this->paymentRepository->getTariffByPayment($ownerId)
        ];
    }
}