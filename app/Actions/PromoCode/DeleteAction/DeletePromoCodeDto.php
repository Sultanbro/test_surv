<?php

namespace App\Actions\PromoCode\DeleteAction;

use App\Actions\FromHttpRequest;
use Illuminate\Foundation\Http\FormRequest;

final class DeletePromoCodeDto
{
    public function __construct(
        public string $code
    )
    {
    }
}