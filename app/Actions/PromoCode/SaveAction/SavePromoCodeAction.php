<?php

namespace App\Actions\PromoCode\SaveAction;

use App\Models\PromoCode;

class SavePromoCodeAction
{
    public function save(SavePromoCodeDto $codeDto): void
    {
        PromoCode::query()->create([
            'code' => $codeDto->code,
            'name' => $codeDto->name,
            'rate' => $codeDto->rate,
            'expired_at' => $codeDto->expired_at
        ]);
    }
}