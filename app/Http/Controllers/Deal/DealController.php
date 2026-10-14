<?php

namespace App\Http\Controllers\Deal;

use App\Http\Controllers\Controller;
use App\Service\Integrations\BitrixIntegrationService;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;

class DealController extends Controller
{
    /**
     * @throws HttpClientException
     */
    public function dealUpdatedWebhook(Request $request, BitrixIntegrationService $bitrix): void
    {
//        $deal_id = $request->input('data')['FIELDS']['ID'];
//        $deal = $bitrix->getDeal($deal_id);

//        UpdateDealJob::dispatch($deal)->delay(now()->addSeconds(2));
    }
}