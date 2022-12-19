<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Service\Settings\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        public UserService $service
    )
    {}

    public function get(Request $request)
    {
        $response = $this->service->get($request->all());

        return $this->response(
            message: "Success",
            data: $response
        );
    }
}
