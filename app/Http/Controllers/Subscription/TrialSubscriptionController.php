<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrialSubscriptionController extends Controller
{

    public function enable(Request $request): JsonResponse
    {
        $tenant = $request->get('tenant_id') ?? tenant('id');

        return $this->response(
            message: 'Success',
            data: []
        );
    }
}
