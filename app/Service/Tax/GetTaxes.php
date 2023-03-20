<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\GetTaxesResponseDTO;
use App\ReadModels\TaxReadModel;

/**
* Класс для работы с Service.
*/
class GetTaxes
{
    public function handle(
        int $userId
    ): GetTaxesResponseDTO
    {
        $taxes = TaxReadModel::getUserTaxes($userId);
        return GetTaxesResponseDTO::fromArray($taxes);
    }
}