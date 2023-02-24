<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\ManagerHasOwner;
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
     * Create new admin 
     * Who can login to admin.jobtron.org
     * 
     * CreateAdminRequest
     * @param Request $request
     * @return JsonResponse 
     */
    public function addAdmin(CreateAdminRequest $request)
    {
        return response()->json([
            'user' => User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => '000000000000',
                'password' => \Hash::make($request->password),
                'is_admin' => 0,
            ])
        ]);
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
    
}
