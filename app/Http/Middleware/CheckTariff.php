<?php

namespace App\Http\Middleware;

use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffPayment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $tariffPlans = $this->tariffPayment
            ->getTarriffPaymentsByOwnerId(\Auth::user()->id);

        $response = $next($request);
        if (!$tariffPlans->isEmpty()){
            $response->header('IsHaveTariff', true);
        }

        return $response;
    }
}
