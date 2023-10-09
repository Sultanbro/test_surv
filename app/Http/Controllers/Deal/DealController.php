<?php

namespace App\Http\Controllers\Deal;

use App\DayType;
use App\Http\Controllers\Controller;
use App\Jobs\Bitrix\UpdateDealJob;
use App\Service\Department\UserService;
use Illuminate\Http\Request;
use App\Api\BitrixOld as Bitrix;
use Exception;
use App\Service\Integrations\BitrixIntegrationService;

class DealController extends Controller
{
    public function dealUpdatedWebhook(Request $request, BitrixIntegrationService $bitrix, UserService $user_service)
    {
        info('Test228Test', $request->all());

        $deal_id = $request->input('data')['FIELDS']['ID'];
        $deal = $bitrix->getDeal($deal_id);

        UpdateDealJob::dispatch($deal)->delay(now()->addSeconds(2));
        return ;
    }
}