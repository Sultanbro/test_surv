<?php

namespace App\Http\Controllers\Mailing;

use App\Http\Controllers\Controller;
use App\Service\SendMessageTraineesService;
use Illuminate\Http\JsonResponse;
use \Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;

class SendWhatsapController extends Controller
{
    /**
     * @param Request $request
     * @param SendMessageTraineesService $service
     * @return JsonResponse
     * @throws HttpClientException
     */
    public function sendMessage(Request $request,SendMessageTraineesService $service):JsonResponse
    {
        return $this->response(
            message: 'Successfully sent messages',
            data: $service->handle($request->user_ids)
        );
    }
}