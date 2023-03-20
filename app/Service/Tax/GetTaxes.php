<?php
declare(strict_types=1);

namespace App\Service\Tax;

use App\DTO\Tax\GetTaxesResponseDTO;
use App\Models\Tax;
use App\User;
use DB;

/**
* Класс для работы с Service.
*/
class GetTaxes
{
    public function handle(
        int $userId
    ): GetTaxesResponseDTO
    {
        $taxes = Tax::query()
            ->select('taxes.id', 'taxes.name', 'taxes.value', 'taxes.is_percent', DB::raw('(user_tax.user_id IS NOT NULL) as isAssigned'))
            ->leftJoin('user_tax', function ($join) use ($userId) {
                $join->on('user_tax.tax_id', '=', 'taxes.id')
                    ->where('user_tax.user_id', '=', $userId);
            })
            ->get()->toArray();

        return GetTaxesResponseDTO::fromArray($taxes);
    }
}