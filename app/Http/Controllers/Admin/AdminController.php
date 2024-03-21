<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CreateOrUpdateAdminRequest;
use Illuminate\Contracts\Foundation\Application;
use App\Repositories\Admin\OwnerRepository;
use App\Service\Admin\UpdateAdminService;
use App\Service\Admin\AddAdminService;
use Illuminate\Contracts\View\Factory;
use App\Models\Admin\ManagerHasOwner;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\CentralUser;
use App\User;
use Throwable;
use Exception;

class AdminController extends Controller
{
    /**
     * Admin.jobtron.org
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin');
    }

    /**
     * Owners list
     *
     * @param Request $request
     * @param OwnerRepository $ownerRepository
     * @return JsonResponse
     */
    public function owners(Request $request, OwnerRepository $ownerRepository): JsonResponse
    {
        $owners = $ownerRepository->getOwnersPaginate($request->get('per_page'), $request);
        $ownerIds = $owners->pluck('id')->toArray();

        // TODO разделить на отдельные endpoint

        return response()->json([
            'items' => $ownerRepository->getOwnersPaginate($request->get('per_page'), $request),
            'manager' => ManagerHasOwner::getOwnersManagers($ownerIds)
        ]);
    }

    /**
     * Admins list
     * Who can log in to admin.jobtron.org
     *
     * @param Request $request
     * @param OwnerRepository $ownerRepository
     * @return JsonResponse
     */
    public function admins(Request $request, OwnerRepository $ownerRepository)
    {
        return response()->json([
            'items' => $ownerRepository->getAdmins()
        ]);
    }

    /**
     * Создаем админа или менеджера.
     *
     * @param CreateOrUpdateAdminRequest $request
     * @param AddAdminService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function addAdmin(CreateOrUpdateAdminRequest $request, AddAdminService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * Delete admin
     * Who can log in to admin.jobtron.org
     *
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function deleteAdmin(int $id): JsonResponse
    {
        /** @var User $user */
        $user = User::withTrashed()->where('id', $id)->first();

        if( !$user ) {
            return response()->json([
                'message' => 'User Not found'
            ], 404);
        }

        /** @var CentralUser $owner */
        $owner = CentralUser::with('tenants')->where('email', $user->email)->first();

        if($owner && $owner->tenants->where('id', 'admin')->first()) {
            return response()->json([
                'message' => 'You can\'t delete owner of admin.jobtron.org'
            ], 403);
        }

        try {
            return response()->success($user->forceDelete());
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param CreateOrUpdateAdminRequest $request
     * @param UpdateAdminService $service
     * @param User $user
     * @return JsonResponse
     */
    public function edit(CreateOrUpdateAdminRequest $request, UpdateAdminService $service, User $user): JsonResponse
    {
        return $this->response(
            message: 'Success updated',
            data: $service->handle($user, $request->toDto())
        );
    }
}
