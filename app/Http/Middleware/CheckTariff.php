<?php

namespace App\Http\Middleware;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\User;
use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckTariff
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $response = $next($request);

        if(tenant('id') == 'bp') {
            return $response;
        }

        $tariffPlan = TariffSubscription::getValidTariffPayment();

        $userLimit = Tariff::$defaultUserLimit;

        if($tariffPlan){
            $userLimit = $tariffPlan->total_user_limit;
            $response->header('X-IsHaveTariff', 1);
        }

        $limitedUser = User::query()->select('id')->skip($userLimit)->first();

        if ($limitedUser) {
            $response->header('X-IsTariffRequired', 1);

            if(\Auth::user()->id >= $limitedUser->id) {
                throw new Exception('Tariff limit out of range', 401);
            }
        }

        return $response;
    }
}
