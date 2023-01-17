<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permissions\SwitchAccessRequest;
use App\Service\Permissions\AccessService;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function __construct(
        private AccessService $accessService
    )
    {
    }

    public function switchAccess(SwitchAccessRequest $request)
    {
        $dto = $request->toDto();
        $response = $this->accessService->handle($dto);
    }
}
