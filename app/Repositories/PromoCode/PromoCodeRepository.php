<?php

namespace App\Repositories\PromoCode;

use App\Models\PromoCode;

class PromoCodeRepository implements PromoCodeRepositoryInterface
{
    public function getAllValidPromoCodes(): array
    {
        $output = [];
        $codes = PromoCode::query()
            ->whereDate('expired_at', '>=', now()->format("Y-m-d"))
            ->get();

        foreach ($codes as $code) {
            $output[] = PromoCodeDto::fromArray($code->toArray());
        }

        return $output;
    }
}