<?php

namespace App\Actions\PromoCode\DeleteAction;

use App\Actions\PromoCode\SaveAction\SavePromoCodeDto;
use App\Models\PromoCode;

class DeletePromoCodeAction
{
    public function delete(DeletePromoCodeDto $codeDto): void
    {
        PromoCode::query()
            ->where('code', $codeDto->code)
            ->delete();
    }
}