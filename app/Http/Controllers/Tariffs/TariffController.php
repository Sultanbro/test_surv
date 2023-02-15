<?php

namespace App\Http\Controllers\Tariffs;

use App\Http\Controllers\Controller;
use App\Models\Tariff\Tariff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        return $this->response(
            message: 'success',
            data: Tariff::all()
        );
    }
}
