<?php

namespace App\Http\Controllers\Tax\Response;
use App\DTO\Tax\GetTaxesResponseDTO;
use Illuminate\Http\JsonResponse;

final class GetTaxesResponse extends JsonResponse {
    
    public static function success(GetTaxesResponseDTO $data) {
        return new GetTaxesResponse($data);
    }
}
