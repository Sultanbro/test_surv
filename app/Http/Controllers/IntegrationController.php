<?php

namespace App\Http\Controllers;

use App\Service\BitrixIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IntegrationController extends Controller
{
    /**
     * @var string $host
     */
    public string $host;

    /**
     * @var string $token
     */
    public string $token;

    /**
     * ShopController constructor.
     *
     */
    public function __construct()
    {
        $this->host  = config('bitrix')['host'];
        $this->token = config('bitrix')['token'];
    }

    /**
     * Тянем все задачи с Bitrix24
     * @param BitrixIntegrationService $service
     * @return JsonResponse
     */
    public function getAllTasksFromBitrix(BitrixIntegrationService $service): JsonResponse
    {
        $response = $service->getTasks();

        return response()->json($response);
    }
}
