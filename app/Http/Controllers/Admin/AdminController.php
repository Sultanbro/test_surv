<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\EditAdminRequest;
use App\Models\Admin\ManagerHasOwner;
use App\Service\Admin\AddAdminService;
use App\Service\Admin\UpdateAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Models\CentralUser;
use App\Repositories\Admin\OwnerRepository;
use App\User;

class AdminController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Admin.jobtron.org
     * 
     * @param Request $request
     */
    public function index(Request $request)
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
        $owners = $ownerRepository->getOwnersPaginate(10, $request);
        $ownerIds = $owners->pluck('id')->toArray();

        // TODO разделить на отдельные endpoint

        return response()->json([
            'items' => $ownerRepository->getOwnersPaginate(10, $request),
            'manager' => ManagerHasOwner::getOwnersManagers($ownerIds)
        ]);
    }

    /**
     * Admins list
     * Who can login to admin.jobtron.org
     * 
     * @param Request $request
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
     * @param CreateAdminRequest $request
     * @param AddAdminService $service
     * @return JsonResponse
     */
    public function addAdmin(CreateAdminRequest $request, AddAdminService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * Delete admin 
     * Who can login to admin.jobtron.org
     * 
     * @param Request $request
     * @return JsonResponse 
     */
    public function deleteAdmin(int $id)
    {       
        $user = User::withTrashed()->where('id', $id)->first();
        
        if( !$user ) {
            return response()->json([
                'message' => 'User Not found'
            ], 404);
        }

        $owner = CentralUser::with('tenants')->where('email', $user->email)->first();

        if($owner && $owner->tenants->where('id', 'admin')->first()) {
            return response()->json([
                'message' => 'You can\'t delete owner of admin.jobtron.org'
            ], 403);
        }

        try {
            return response()->success($user->forceDelete());
        }catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    public function edit(EditAdminRequest $request, UpdateAdminService $service, User $user)
    {

        return $this->response(
            message: 'Success updated',
            data: $service->handle($user, $request->toDto())
        );
    }
}
