<?php

namespace App\ReadModels;

use App\Models\Tax;
use Illuminate\Support\Facades\DB;

class TaxReadModel
{
    public static function getUserTaxes(
        int $userId
    ): array
    {
        return Tax::query()
            ->select('taxes.id', 'taxes.name', 'taxes.value', 'taxes.is_percent', DB::raw('(user_tax.user_id IS NOT NULL) as isAssigned'))
            ->leftJoin('user_tax', function ($join) use ($userId) {
                $join->on('user_tax.tax_id', '=', 'taxes.id')
                    ->where('user_tax.user_id', '=', $userId);
            })
            ->get()->toArray();
    }
}