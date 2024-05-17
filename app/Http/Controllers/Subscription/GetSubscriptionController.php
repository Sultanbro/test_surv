<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Service\Admin\Owners\OwnerInfoService;
use App\Service\Payment\Core\CanCalculateTariffPrice;
use App\Service\Payment\Core\TariffListService;
use Exception;
use Illuminate\Http\JsonResponse;

class GetSubscriptionController extends Controller
{
    use CanCalculateTariffPrice;

    /**
     * @param TariffListService $tariffGetAllService
     */
    public function __construct(
        public TariffListService $tariffGetAllService
    )
    {
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(): JsonResponse
    {
        /** @var OwnerInfoService $service */
        $service = app(OwnerInfoService::class);

        return $this->response(
            message: 'success',
            data: $service->handle()
        );
    }
}
