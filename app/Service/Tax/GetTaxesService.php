<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\GetTaxesResponseDTO;
use App\Repositories\TaxRepository;

/**
* Класс для работы с Service.
*/
class GetTaxesService
{
    public function handle(
        int $userId
    ): GetTaxesResponseDTO
    {
        $taxes = (new TaxRepository)->getUserTaxes($userId);
        dd($taxes);
        return GetTaxesResponseDTO::fromArray($taxes);
    }
}
