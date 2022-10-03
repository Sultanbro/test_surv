<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Zz;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Проверяем доступ.
     * @return void
     */
    protected function access(): void
    {
        $user = Auth::user()->is_admin ?? User::find(5)->is_admin;
        abort_if(!$user, Response::HTTP_FORBIDDEN, "You don't have permission to add award!");
    }
}
