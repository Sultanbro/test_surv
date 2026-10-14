<?php

namespace App\Actions\PromoCode\SaveAction;

use App\Actions\FromHttpRequest;
use Illuminate\Foundation\Http\FormRequest;

final class SavePromoCodeDto implements FromHttpRequest
{
    public function __construct(
        public string $name,
        public string $code,
        public string $rate,
        public string $expired_at
    )
    {
    }

    public static function fromRequest(FormRequest $request): SavePromoCodeDto
    {
        $data = $request->validated();
        return new self(
            $data['name'],
            $data['code'],
            $data['rate'],
            $data['expired_at']
        );
    }
}