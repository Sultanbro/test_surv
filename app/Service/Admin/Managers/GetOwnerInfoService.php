<?php

namespace App\Service\Admin\Managers;

use App\DTO\Manager\GetOwnerInfoDTO;
use App\Enums\ErrorCode;
use App\Models\CentralUser;
use App\Repositories\Tariffs\TariffPaymentRepository;
use App\Support\Core\CustomException;

/**
 * Класс для работы с Service.
 */
class GetOwnerInfoService
{

    public function __construct(
        public TariffPaymentRepository $paymentRepository
    )
    {
    }

    /**
     * @param int $ownerId
     * @return array
     */
    public function handle(
        int $ownerId
    ): array
    {
        $owner = CentralUser::query()->find($ownerId);

        if ($owner == null) {
            new CustomException("User with $ownerId is not exist", ErrorCode::BAD_REQUEST, []);
        }

        return [
            'owner' => $owner,
            'tariff' => $this->paymentRepository->getTariffByPayment($ownerId)
        ];
    }
}