<?php

namespace App\Http\Middleware;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\User;
use Closure;
use Illuminate\Http\Request;

class CheckTariff
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next)
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

        $limitedUser = User::select('id')->skip($userLimit)->first();

        if ($limitedUser) {
            $response->header('X-IsTariffRequired', 1);

            if(\Auth::user()->id >= $limitedUser->id) {
                throw new \Exception('Tariff limit out of range', 401);
            }
        }

        return $response;
    }
}
