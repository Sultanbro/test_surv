<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermissionController extends Controller
{
    /**
     * Список всех доступов.
     *
     * @return JsonResponse
     */
    public function getPermissions(): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: Permission::all()
        );
    }

    /**
     * Список всех ролей.
     *
     * @return JsonResponse
     */
    public function getRoles(): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: Role::all()
        );
    }
}
