<<<<<<< HEAD
=======
<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Service\Payments\Core\TariffGetAllService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TariffController extends Controller
{
    /**
     * @param TariffGetAllService $tariffGetAllService
     */
    public function __construct(
        public TariffGetAllService $tariffGetAllService
    )
    {
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function get(): JsonResponse
    {
        return $this->response(
            message: 'success',
            data: $this->tariffGetAllService->handle(),
//            data: Cache::driver('central')
//                ->remember('currencies', 60 * 24, fn() => $this->tariffGetAllService->handle()),
        );
    }
}
>>>>>>> f7e5089d51c7b1667e0d4a0a7384479dc36dee6f
