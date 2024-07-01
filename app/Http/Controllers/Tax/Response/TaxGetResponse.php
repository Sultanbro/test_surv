<?php

namespace App\Http\Controllers\Tax\Response;
use App\DTO\Tax\GetTaxesResponseDTO;
use Illuminate\Http\JsonResponse;

final class TaxGetResponse extends JsonResponse {
    
    public static function success(GetTaxesResponseDTO $data): self
    {
        return new TaxGetResponse($data);
    }
}
