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
    ): array
    {
        return [
            'user_taxes' => (new TaxRepository)->getUserTaxes($userId),
            'taxes' => (new TaxRepository)->getArray()
        ];
    }
}
