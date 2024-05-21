<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreateSubscriptionRequest;
use App\Service\Subscription\Pipeline\SubscriptionPipeline;
use Exception;
use Illuminate\Http\JsonResponse;

class NewSubscriptionController extends Controller
{
    /**
     * @param CreateSubscriptionRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function __invoke(CreateSubscriptionRequest $request): JsonResponse
    {
        $pipeline = new SubscriptionPipeline($request->toDto());
        $pipeline->apply();

        return $this->response(
            message: 'Success',
            data: $pipeline->invoice()
        );
    }
}
