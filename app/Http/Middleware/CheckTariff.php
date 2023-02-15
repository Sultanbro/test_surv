<?php

namespace App\Http\Middleware;

use App\Enums\Payments\PaymentStatusEnum;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use App\User;
use Closure;
use Illuminate\Http\Request;

class CheckTariff
{
    public function __construct(
        private $tariffPayment = new TariffPayment()
    ){}

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

        $tariffPlan = $this->tariffPayment
            ->where('status', PaymentStatusEnum::STATUS_SUCCESS)
            ->getValidTarriffPayments();

        $userLimit = Tariff::$defaultUserLimit;

        if($tariffPlan){
            $userLimit = $tariffPlan->total_user_limit;
            $response->header('IsHaveTariff', 1);
        }

        $users = User::select('id')->skip($userLimit)->first();

        if(\Auth::user()->id > $users->id) throw new \Exception('Tariff limit out of range', 401);

        return $response;
    }
}
