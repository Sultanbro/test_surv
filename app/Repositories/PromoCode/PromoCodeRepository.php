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

    public function getValidItemByCode(string $code): PromoCodeDto
    {
        $code = PromoCode::query()
            ->whereDate('expired_at', '>=', now()->format("Y-m-d"))
            ->where('code', $code)
            ->firstOrFail();

        return PromoCodeDto::fromArray($code->toArray());
    }

    public function exitsValidItem(string $code): bool
    {
        return PromoCode::query()
            ->whereDate('expired_at', '>=', now()->format("Y-m-d"))
            ->where('code', $code)
            ->exists();
    }
}